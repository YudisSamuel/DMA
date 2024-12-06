<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Peramalan</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>
<body>
    <div class="content-wrapper">
        <div class="header">
            <div class="company-name">JIGZLE</div>
            <h1 class="report-title">Laporan Hasil Peramalan</h1>
        </div>

        <div class="metadata">
            <div class="metadata-item">
                <strong>Tanggal Laporan:</strong> {{ date('d F Y') }}
            </div>
            <div class="metadata-item">
                <strong>Periode Peramalan:</strong> {{ $peramalanData->Bulan }}
            </div>
            <div class="metadata-item">
                <strong>Dibuat Oleh:</strong> Admin Jigzle
            </div>
        </div>

        <div class="analysis-section">
            <h2>Detail Hasil Peramalan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Tipe Produk</th>
                        <th>Hasil Peramalan</th>
                        <th>MAPE (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $peramalanData->Bulan }}</td>
                        <td>{{ $peramalanData->Type_Produk }}</td>
                        <td>{{ number_format($peramalanData->Prediksi, 0, ',', '.') }}</td>
                        <td>
                            <span class="mape-indicator {{ $peramalanData->mape < 10 ? 'success' : ($peramalanData->mape < 20 ? 'warning' : 'danger') }}">
                                {{ number_format($peramalanData->mape, 2) }}%
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="analysis-section">
            <h2>Interpretasi Hasil</h2>
            <p>Berdasarkan hasil peramalan untuk {{ $peramalanData->Type_Produk }} pada periode {{ $peramalanData->Bulan }},
               dapat disimpulkan bahwa:</p>
            <ul>
                <li>Nilai peramalan menunjukkan prediksi sebesar {{ number_format($peramalanData->Prediksi, 0, ',', '.') }} unit</li>
                <li>Tingkat akurasi peramalan (MAPE) adalah {{ number_format($peramalanData->mape, 2) }}%, yang menunjukkan
                    @if($peramalanData->mape < 10)
                        tingkat akurasi sangat baik
                    @elseif($peramalanData->mape <= 20)
                        tingkat akurasi baik
                    @elseif($peramalanData->mape <= 50)
                        tingkat akurasi cukup baik
                    @else
                        perlu peningkatan akurasi
                    @endif
                </li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem peramalan.</p>
        <p>© {{ date('Y') }} JIGZLE.</p>
    </div>
</body>
</html>
