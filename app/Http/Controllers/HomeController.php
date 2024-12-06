<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataProduct; // Import model DataProduct
use App\Models\Peramalan; // Import model Peramalan

class HomeController extends Controller
{

// HomeController
public function index(Request $request){
    $year = $request->input('year', 2024); // Ambil tahun dari request (default 2024)

    // Data Home dengan filter tahun
    $totalPenjualan = DataProduct::whereYear('Tanggal', $year)->sum('Jumlah_Terjual');
    $penjualanBulanan = DataProduct::selectRaw('MONTH(Tanggal) as bulan, SUM(Jumlah_Terjual) as total')
        ->whereYear('Tanggal', $year)
        ->groupBy('bulan')
        ->pluck('total', 'bulan')
        ->toArray();

    $keuntunganBulanan = DataProduct::selectRaw('MONTH(Tanggal) as bulan, SUM(Jumlah_Terjual * Harga_Produk) as keuntungan')
        ->whereYear('Tanggal', $year)
        ->groupBy('bulan')
        ->pluck('keuntungan', 'bulan')
        ->toArray();

    // Data prediksi terbaru
    $latestPrediction = Peramalan::orderBy('id', 'desc')->first();

    return view('Home', [
        'totalPenjualan' => $totalPenjualan,
        'penjualanBulanan' => array_values(array_replace(array_fill(1, 12, 0), $penjualanBulanan)),
        'keuntungan' => array_values(array_replace(array_fill(1, 12, 0), $keuntunganBulanan)),
        'totalKeuntungan' => array_sum($keuntunganBulanan),
        'year' => $year, // Tahun aktif
        'latestPrediction' => $latestPrediction
    ]);
}

}
