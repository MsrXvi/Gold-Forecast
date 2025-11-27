<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HargaEmas;
use App\Models\Prediksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil harga emas hari ini
        $hargaHariIni = HargaEmas::orderBy('tanggal', 'desc')->first();

        // Ambil harga emas 1 bulan lalu
        $hargaBulanLalu = HargaEmas::whereDate('tanggal', '<=', now()->subMonth())
            ->orderBy('tanggal', 'desc')
            ->first();

        // Hitung perubahan persen
        $perubahanPersen = 0;
        if ($hargaBulanLalu && $hargaHariIni) {
            $perubahanPersen = (($hargaHariIni->harga_usd - $hargaBulanLalu->harga_usd) / $hargaBulanLalu->harga_usd) * 100;
        }

        // Total data
        $totalData = HargaEmas::count();
        $totalPrediksi = Prediksi::count();

        // Data untuk grafik (1 tahun terakhir)
        $dataGrafik = HargaEmas::where('tanggal', '>=', now()->subYear())
            ->orderBy('tanggal', 'asc')
            ->select('tanggal', 'harga_usd', 'harga_idr')
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => $item->tanggal->format('Y-m-d'),
                    'harga_usd' => (float) $item->harga_usd,
                    'harga_idr' => (float) $item->harga_idr
                ];
            });

        return view('dashboard', [
            'header' => 'Dashboard',
            'showUpdateButton' => true,
            'hargaHariIni' => $hargaHariIni,
            'hargaBulanLalu' => $hargaBulanLalu,
            'perubahanPersen' => $perubahanPersen,
            'totalData' => $totalData,
            'totalPrediksi' => $totalPrediksi,
            'dataGrafik' => $dataGrafik,
        ]);
    }

    public function updateHarga(Request $request)
    {
        try {
            Log::info('=== UPDATE HARGA DIMULAI ===');

            // FIX: Set PDO attributes untuk mencegah error buffered query
            try {
                $pdo = DB::connection()->getPdo();
                $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            } catch (\Exception $e) {
                Log::warning('Cannot set PDO attributes: ' . $e->getMessage());
            }

            // Clear any existing unbuffered queries
            DB::disconnect();
            DB::reconnect();

            // Cek jumlah data sebelum - gunakan query builder yang lebih aman
            $countBefore = DB::table('tb_harga_emas')->count();
            $latestBefore = DB::table('tb_harga_emas')
                ->orderBy('tanggal', 'desc')
                ->first();

            Log::info('Data sebelum update:', [
                'count' => $countBefore,
                'latest' => $latestBefore ? $latestBefore->tanggal : null
            ]);

            // Path Python dari config
            $pythonPath = config('app.python_path', env('PYTHON_PATH', 'python3'));
            $scriptPath = base_path('python_scripts/update_data.py');
            $workingDir = base_path();

            // Cek file
            if (!file_exists($scriptPath)) {
                throw new \Exception("Script tidak ditemukan: {$scriptPath}");
            }

            // Pastikan file .env ada dan bisa dibaca
            $envPath = base_path('.env');
            if (!file_exists($envPath)) {
                throw new \Exception(".env file tidak ditemukan");
            }

            Log::info('File paths:', [
                'python' => $pythonPath,
                'script' => $scriptPath,
                'env' => $envPath,
                'working_dir' => $workingDir
            ]);

            // Build command - Python akan membaca .env sendiri
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows: Jalankan dari working directory, biarkan Python baca .env
                $command = sprintf(
                    'cd /d "%s" && "%s" "%s" today 2>&1',
                    $workingDir,
                    $pythonPath,
                    $scriptPath
                );
            } else {
                // Linux/Mac
                $command = sprintf(
                    'cd "%s" && "%s" "%s" today 2>&1',
                    $workingDir,
                    $pythonPath,
                    $scriptPath
                );
            }

            Log::info('Executing command:', ['cmd' => $command]);

            // Execute dengan timeout
            $output = [];
            $return_var = 0;

            // Set timeout 120 detik
            set_time_limit(120);

            exec($command, $output, $return_var);

            $outputString = implode("\n", $output);

            Log::info('Python execution completed:', [
                'return_code' => $return_var,
                'output_length' => strlen($outputString),
                'output' => $outputString
            ]);

            // Cek return code
            if ($return_var !== 0) {
                throw new \Exception("Python script failed with return code {$return_var}:\n{$outputString}");
            }

            // Cek error keywords
            $outputLower = strtolower($outputString);
            if (strpos($outputLower, '[error]') !== false) {
                // Ambil baris error
                $lines = explode("\n", $outputString);
                $errors = array_filter($lines, function($line) {
                    return stripos($line, '[error]') !== false;
                });
                throw new \Exception("Python script error:\n" . implode("\n", $errors));
            }

            // Parse JSON result jika ada
            $saved = 0;
            $updated = 0;
            if (preg_match('/\[JSON_RESULT\](.*?)\[\/JSON_RESULT\]/s', $outputString, $matches)) {
                $jsonData = json_decode($matches[1], true);
                if ($jsonData) {
                    $saved = $jsonData['saved'] ?? 0;
                    $updated = $jsonData['updated'] ?? 0;
                    Log::info('Parsed JSON result:', $jsonData);
                }
            }

            // Tunggu dan refresh connection dengan cara yang lebih aman
            sleep(2);

            // Disconnect dan reconnect dengan clear
            DB::disconnect();
            sleep(1);
            DB::reconnect();

            // Set PDO attributes lagi setelah reconnect
            try {
                $pdo = DB::connection()->getPdo();
                $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

                // Clear cache dengan query sederhana
                DB::select('SELECT 1');
            } catch (\Exception $e) {
                Log::warning('Cannot refresh connection: ' . $e->getMessage());
            }

            // Cek data setelah - gunakan query builder
            $countAfter = DB::table('tb_harga_emas')->count();
            $latestAfter = DB::table('tb_harga_emas')
                ->orderBy('tanggal', 'desc')
                ->first();

            Log::info('Data setelah update:', [
                'count' => $countAfter,
                'latest' => $latestAfter ? $latestAfter->tanggal : null,
                'new_records' => $countAfter - $countBefore
            ]);

            $newRecords = $countAfter - $countBefore;

            // Build message
            $today = now();
            $isWeekend = in_array($today->dayOfWeek, [0, 6]);

            $daysDiff = 0;
            if ($latestAfter && $latestAfter->tanggal) {
                $latestDate = \Carbon\Carbon::parse($latestAfter->tanggal);
                $daysDiff = $today->diffInDays($latestDate);
            }

            if ($newRecords > 0) {
                $message = "Berhasil menambahkan {$newRecords} data baru!";
            } else if ($saved > 0 || $updated > 0) {
                $message = "Update selesai: {$saved} data baru, {$updated} data diupdate";
            } else if ($daysDiff > 0 && $isWeekend) {
                $message = "Database sudah up-to-date! Pasar tutup di weekend.";
            } else {
                $message = "Database sudah up-to-date!";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'before' => [
                        'count' => $countBefore,
                        'latest_date' => $latestBefore ? $latestBefore->tanggal : null,
                        'latest_price' => $latestBefore ? (float) $latestBefore->harga_idr : null
                    ],
                    'after' => [
                        'count' => $countAfter,
                        'latest_date' => $latestAfter ? $latestAfter->tanggal : null,
                        'latest_price' => $latestAfter ? (float) $latestAfter->harga_idr : null,
                        'latest_price_usd' => $latestAfter ? (float) $latestAfter->harga_usd : null
                    ],
                    'new_records' => $newRecords,
                    'days_diff' => $daysDiff,
                    'today' => [
                        'date' => $today->format('Y-m-d'),
                        'day_of_week' => $today->dayOfWeek,
                        'is_weekend' => $isWeekend
                    ],
                    'python_stats' => [
                        'saved' => $saved,
                        'updated' => $updated
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error update harga:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clear connection saat error dengan cara yang lebih aman
            try {
                DB::disconnect();
                sleep(1);
                DB::reconnect();

                // Set PDO attributes
                $pdo = DB::connection()->getPdo();
                $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            } catch (\Exception $dbError) {
                Log::error('Error reconnecting DB: ' . $dbError->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
