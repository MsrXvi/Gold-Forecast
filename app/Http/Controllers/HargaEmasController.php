<?php

namespace App\Http\Controllers;

use App\Models\HargaEmas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HargaEmasController extends Controller
{
    public function index(Request $request)
    {
        $query = HargaEmas::query();

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Pagination
        $hargaEmas = $query->orderBy('tanggal', 'desc')->paginate(50);

        // Data untuk grafik
        $dataGrafik = HargaEmas::orderBy('tanggal', 'asc')
            ->get(['tanggal', 'harga_usd', 'harga_idr']);

        return view('harga-emas.index', compact('hargaEmas', 'dataGrafik'));
    }

    public function refresh(Request $request)
    {
        // Validasi input
        $request->validate([
            'years' => 'required|integer|min:1|max:20'
        ], [
            'years.required' => 'Jumlah tahun harus diisi',
            'years.integer' => 'Jumlah tahun harus berupa angka',
            'years.min' => 'Jumlah tahun minimal 1 tahun',
            'years.max' => 'Jumlah tahun maksimal 20 tahun'
        ]);

        try {
            $years = $request->input('years', 5);

            // Cek data sebelum update
            $dataBefore = HargaEmas::count();

            // Konfigurasi path Python
            $pythonPath = config('app.python_path', 'python');
            $scriptPath = base_path('python_scripts/update_data2.py');

            // Pastikan file script ada
            if (!file_exists($scriptPath)) {
                Log::error("Python script not found: " . $scriptPath);
                return redirect()->route('harga-emas.index')
                    ->with('error', 'Script Python tidak ditemukan di: ' . $scriptPath);
            }

            Log::info("Starting gold data update for $years years");
            Log::info("Python path: $pythonPath");
            Log::info("Script path: $scriptPath");

            // Eksekusi script Python dengan timeout yang lebih lama
            $command = escapeshellcmd("$pythonPath $scriptPath historical $years") . " 2>&1";

            Log::info("Executing command: $command");

            // Set timeout lebih lama untuk data banyak
            set_time_limit(600); // 10 menit

            $output = shell_exec($command);

            Log::info("Python script output: " . $output);

            // Cek data setelah update
            $dataAfter = HargaEmas::count();
            $dataAdded = $dataAfter - $dataBefore;

            // Cek apakah ada error dari Python
            if ($output && (strpos($output, 'Error') !== false || strpos($output, 'Traceback') !== false)) {
                Log::error("Python script error: " . $output);
                return redirect()->route('harga-emas.index')
                    ->with('error', 'Terjadi kesalahan saat mengambil data. Silakan cek log untuk detail.');
            }

            // Cek apakah data bertambah
            if ($dataAdded > 0) {
                Log::info("Successfully added $dataAdded records");
                return redirect()->route('harga-emas.index')
                    ->with('success', "Berhasil menambahkan $dataAdded data harga emas untuk periode $years tahun terakhir");
            } else {
                Log::warning("No new data added. Before: $dataBefore, After: $dataAfter");
                return redirect()->route('harga-emas.index')
                    ->with('success', "Proses selesai. Data sudah up-to-date (tidak ada data baru untuk periode $years tahun)");
            }

        } catch (\Exception $e) {
            Log::error("Error updating gold data: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return redirect()->route('harga-emas.index')
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $hargaEmas = HargaEmas::findOrFail($id);
            $hargaEmas->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function grafikBulanan()
    {
        // Data harga emas per bulan (rata-rata)
        $dataGrafik = HargaEmas::selectRaw('
                YEAR(tanggal) as tahun,
                MONTH(tanggal) as bulan,
                AVG(harga_usd) as harga_rata
            ')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        return response()->json($dataGrafik);
    }

    /**
     * Method untuk testing koneksi Python script
     */
    public function testPythonConnection()
    {
        try {
            $pythonPath = config('app.python_path', 'python');
            $scriptPath = base_path('python_scripts/update_data2.py');

            // Test 1: Cek Python version
            $pythonVersion = shell_exec("$pythonPath --version 2>&1");

            // Test 2: Cek script exists
            $scriptExists = file_exists($scriptPath);

            // Test 3: Cek database connection dari Python
            $command = escapeshellcmd("$pythonPath $scriptPath stats") . " 2>&1";
            $output = shell_exec($command);

            return response()->json([
                'python_version' => $pythonVersion,
                'script_exists' => $scriptExists,
                'script_path' => $scriptPath,
                'test_output' => $output
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
