<?php
namespace App\Http\Controllers;

use App\Models\Prediksi;
use App\Models\Akurasi;
use App\Models\HargaEmas;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        // Ringkasan prediksi
        $hargaTertinggi = Prediksi::max('harga_prediksi_idr');
        $hargaTerendah = Prediksi::min('harga_prediksi_idr');
        $hargaRataRata = Prediksi::avg('harga_prediksi_idr');

        // Akurasi model
        $akurasi = Akurasi::latest()->first();

        // Data prediksi
        $dataPrediksi = Prediksi::orderBy('tanggal_prediksi', 'asc')->get();

        // Grafik tren
        $trendData = Prediksi::selectRaw('
                YEAR(tanggal_prediksi) as tahun,
                AVG(harga_prediksi_idr) as harga_rata
            ')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        return view('laporan.index', compact(
            'hargaTertinggi',
            'hargaTerendah',
            'hargaRataRata',
            'akurasi',
            'dataPrediksi',
            'trendData'
        ));
    }

    public function downloadPdf()
    {
        $data = [
            'tanggal' => Carbon::now()->format('d M Y'),
            'hargaTertinggi' => Prediksi::max('harga_prediksi_idr'),
            'hargaTerendah' => Prediksi::min('harga_prediksi_idr'),
            'hargaRataRata' => Prediksi::avg('harga_prediksi_idr'),
            'akurasi' => Akurasi::latest()->first(),
            'dataPrediksi' => Prediksi::orderBy('tanggal_prediksi', 'asc')->get(),
            'dataHistoris' => HargaEmas::orderBy('tanggal', 'desc')->take(30)->get()
        ];

        $pdf = Pdf::loadView('laporan.pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('laporan-prediksi-emas-' . date('Y-m-d') . '.pdf');
    }
}
