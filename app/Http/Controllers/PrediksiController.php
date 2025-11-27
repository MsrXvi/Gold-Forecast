<?php

namespace App\Http\Controllers;

use App\Models\Prediksi;
use App\Models\Akurasi;
use App\Models\HargaEmas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrediksiController extends Controller
{
    public function index()
    {
        // Ambil prediksi terbaru
        $prediksiData = Prediksi::orderBy('tanggal_prediksi', 'asc')->get();

        // Ambil akurasi terbaru
        $akurasi = Akurasi::latest()->first();

        // Data historis untuk perbandingan
        $dataHistoris = HargaEmas::orderBy('tanggal', 'asc')
            ->take(100)
            ->get(['tanggal', 'harga_usd']);

        return view('prediksi.index', compact('prediksiData', 'akurasi', 'dataHistoris'));
    }

    public function create()
    {
        return view('prediksi.create');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'periode_tahun' => 'required|integer|min:1|max:10'
        ]);

        try {
            $periodeTahun = $request->periode_tahun;

            $pythonPath = config('app.python_path', 'python');
            $scriptPath = base_path('python_scripts/peramalan.py');

            // Jalankan script Python
            $command = "$pythonPath $scriptPath $periodeTahun 2>&1";
            $output = shell_exec($command);

            // Cek apakah berhasil
            if (strpos($output, 'PROSES SELESAI') !== false) {
                return redirect()->route('prediksi.index')
                    ->with('success', "Prediksi berhasil dibuat untuk $periodeTahun tahun ke depan");
            } else {
                return redirect()->route('prediksi.create')
                    ->with('error', 'Gagal membuat prediksi: ' . $output);
            }

        } catch (\Exception $e) {
            return redirect()->route('prediksi.create')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function grafik()
    {
        // Data untuk grafik perbandingan aktual vs prediksi
        $dataHistoris = HargaEmas::orderBy('tanggal', 'desc')
            ->take(365)
            ->get(['tanggal', 'harga_usd', 'harga_idr'])
            ->reverse()
            ->values();

        $dataPrediksi = Prediksi::orderBy('tanggal_prediksi', 'asc')
            ->get(['tanggal_prediksi as tanggal', 'harga_prediksi_usd as harga_usd', 'harga_prediksi_idr as harga_idr']);

        return response()->json([
            'historis' => $dataHistoris,
            'prediksi' => $dataPrediksi
        ]);
    }
}


