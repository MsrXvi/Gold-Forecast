{{-- resources/views/laporan/pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Prediksi Harga Emas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .section {
            margin-bottom: 25px;
        }

        .section h2 {
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stats-item {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .stats-item .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .stats-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN PREDIKSI HARGA EMAS</h1>
        <p>Metode Prophet</p>
        <p>Tanggal: {{ $tanggal }}</p>
    </div>

    <div class="section">
        <h2>Ringkasan Hasil Prediksi</h2>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="label">Harga Tertinggi</div>
                <div class="value">Rp{{ number_format($hargaTertinggi, 0, ',', '.') }}</div>
            </div>
            <div class="stats-item">
                <div class="label">Harga Terendah</div>
                <div class="value">Rp{{ number_format($hargaTerendah, 0, ',', '.') }}</div>
            </div>
            <div class="stats-item">
                <div class="label">Harga Rata-rata</div>
                <div class="value">Rp{{ number_format($hargaRataRata, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    @if ($akurasi)
        <div class="section">
            <h2>Metrik Akurasi Model</h2>
            <table>
                <tr>
                    <th>Metrik</th>
                    <th>Nilai</th>
                    <th>Keterangan</th>
                </tr>
                <tr>
                    <td>MAPE</td>
                    <td>{{ number_format($akurasi->mape, 2) }}%</td>
                    <td>Mean Absolute Percentage Error</td>
                </tr>
                <tr>
                    <td>RMSE</td>
                    <td>{{ number_format($akurasi->rmse, 2) }}</td>
                    <td>Root Mean Square Error</td>
                </tr>
                <tr>
                    <td>MAE</td>
                    <td>{{ number_format($akurasi->mae, 2) }}</td>
                    <td>Mean Absolute Error</td>
                </tr>
                <tr>
                    <td>R²</td>
                    <td>{{ number_format($akurasi->r_squared, 4) }}</td>
                    <td>Coefficient of Determination</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="section">
        <h2>Prediksi Harga (Sample Bulanan)</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Harga USD/oz</th>
                    <th>Harga IDR/gram</th>
                    <th>Rentang</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataPrediksi as $index => $item)
                    @if ($index % 30 == 0)
                        <tr>
                            <td>{{ $item->tanggal_prediksi->format('d M Y') }}</td>
                            <td>${{ number_format($item->harga_prediksi_usd, 2) }}</td>
                            <td>Rp {{ number_format($item->harga_prediksi_idr, 0, ',', '.') }}</td>
                            <td>Rp
                                {{ number_format(($item->lower_bound * 31.1035 * 16650) / 31.1035, 0, ',', '.') }} -
                                Rp
                                {{ number_format(($item->upper_bound * 31.1035 * 16650) / 31.1035, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Data Historis Terakhir</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Harga USD/oz</th>
                    <th>Harga IDR/gram</th>
                    <th>Perubahan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataHistoris as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                        <td>${{ number_format($item->harga_usd, 2) }}</td>
                        <td>Rp {{ number_format($item->harga_idr, 0, ',', '.') }}</td>
                        <td>{{ $item->perubahan_persen >= 0 ? '+' : '' }}{{ number_format($item->perubahan_persen, 2) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem peramalan harga emas</p>
        <p>&copy; {{ date('M Y') }} - Web Peramalan Harga Emas</p>
        <p>Created by MSR</p>
    </div>
</body>

</html>
