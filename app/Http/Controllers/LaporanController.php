<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataProduct;
use App\Models\Peramalan;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    // app/Http/Controllers/LaporanController.php
public function index(Request $request)
{
    // Ambil semua Type_Produk unik dari tb_DataProduct
    $uniqueTypes = DataProduct::pluck('Type_Produk')->unique();

    // Ambil tipe produk dari request filter (jika ada)
    $typeFilter = $request->input('typeFilter');

    // Ambil data peramalan dan filter berdasarkan is_deleted = 0
    $peramalanData = Peramalan::where('is_deleted', false)
        ->when($typeFilter, function ($query, $typeFilter) {
            return $query->where('Type_Produk', $typeFilter);
        })
        ->get();

    // Ambil prediksi terakhir berdasarkan Bulan
    $latestPrediction = Peramalan::where('is_deleted', false)
        ->orderBy('Bulan', 'desc')
        ->first();

    return view('laporan', compact('peramalanData', 'uniqueTypes', 'typeFilter', 'latestPrediction'));
}



    public function cetak($id)
    {
        // Ambil data peramalan berdasarkan ID yang belum dihapus
        $peramalanData = Peramalan::where('id', $id)->where('is_deleted', false)->firstOrFail();

        // Load view PDF dan kirimkan data
        $pdf = Pdf::loadView('pdf.laporan-pdf', compact('peramalanData'));

        // Download PDF
        return $pdf->download('laporan_peramalan_' . $id . '.pdf');
    }


    public function clear(Request $request)
    {
        // Ubah status is_deleted menjadi 1 pada data yang sesuai
        Peramalan::where('is_deleted', 0)->update(['is_deleted' => 1]);

        // Kembalikan respons JSON agar dapat menampilkan SweetAlert
        return response()->json(['message' => 'Data berhasil dihapus sementara.']);
    }




    public function restore()
{
    // Ubah semua data is_deleted menjadi false
    Peramalan::where('is_deleted', true)->update(['is_deleted' => false]);

    // Redirect kembali ke halaman laporan dengan pesan sukses
    return redirect()->route('laporan')->with('success', 'Semua data berhasil dipulihkan.');
}

}
