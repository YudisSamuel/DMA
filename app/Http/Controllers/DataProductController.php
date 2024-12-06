<?php

namespace App\Http\Controllers;

use App\Models\DataProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Statement;

class DataProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 25);
        $page = $request->input('page', 1);
        $type = $request->input('type', '');
        $query = DataProduct::query();

        if (!empty($type)) {
            $query->where('Type_Produk', $type);
        }

        $DataProduct = $query->paginate($perPage, ['*'], 'page', $page);

        $uniqueTypes = DataProduct::select('Type_Produk')
            ->distinct()
            ->pluck('Type_Produk');

        // Kirim data ke view
        return view('dataproduct', [
            'DataProduct' => $DataProduct,
            'uniqueTypes' => $uniqueTypes,
            'currentPage' => $DataProduct->currentPage(),
            'lastPage' => $DataProduct->lastPage(),
            'perPage' => $perPage,
            'selectedType' => $type,
            'totalDataProduct' => $DataProduct->total(),
        ]);
    }

    public function store(Request $request)
    {
        // Log untuk melihat apakah data sampai ke controller
        logger()->info('Data yang diterima:', $request->all());

        $request->validate([
            'tanggal' => 'required|date',
            'kode_produk' => 'required|string|max:255',
            'type_produk' => 'required|string|max:255',
            'jumlah_terjual' => 'required|integer',
            'harga_produk' => 'required|numeric',
        ]);

        // Simpan data ke database
        $dataProduct = new DataProduct();
        $dataProduct->Tanggal = $request->tanggal;
        $dataProduct->Kode_Produk = $request->kode_produk;
        $dataProduct->Type_Produk = $request->type_produk;
        $dataProduct->Jumlah_Terjual = $request->jumlah_terjual;
        $dataProduct->Harga_Produk = $request->harga_produk;
        $dataProduct->save();

        logger()->info('Data disimpan:', $dataProduct->toArray());

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        // Mengambil data produk berdasarkan ID
        $dataProduct = DataProduct::findOrFail($id);

        // Kirim data produk ke view edit
        return view('dataproduct.edit', compact('dataProduct'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'kode_produk' => 'required|string|max:255',
            'type_produk' => 'required|string|max:255',
            'jumlah_terjual' => 'required|integer',
            'harga_produk' => 'required|numeric',
        ]);

        // Temukan produk yang akan diperbarui
        $dataProduct = DataProduct::findOrFail($id);

        // Perbarui data produk
        $dataProduct->Tanggal = $request->tanggal;
        $dataProduct->Kode_Produk = $request->kode_produk;
        $dataProduct->Type_Produk = $request->type_produk;
        $dataProduct->Jumlah_Terjual = $request->jumlah_terjual;
        $dataProduct->Harga_Produk = $request->harga_produk;
        $dataProduct->save();

        // Log info update produk
        logger()->info('Data diperbarui:', $dataProduct->toArray());

        // Redirect dengan pesan sukses
        return redirect()->route('DataProduct')->with('success', 'Data Produk berhasil diperbarui.');

    }


    public function show($id)
    {
        $product = DataProduct::find($id);

        // If the product is not found, redirect with a message
        if (!$product) {
            return redirect()->route('DataProduct')->with('error', 'Product not found');
        }

        return view('dataproduct.show', compact('product'));
    }


    public function destroy($id) {
        try {
            $product = DataProduct::find($id);

            if (!$product) {
                logger()->info('Produk tidak ditemukan dengan ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.'
                ], 404);
            }

            $product->delete();
            logger()->info('Produk berhasil dihapus: ', $product->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Data Produk berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            logger()->error('Kesalahan saat menghapus produk: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data produk.'
            ], 500);
        }
    }



    public function uploadCsv(Request $request)
    {
        try {
            if (!$request->hasFile('csvFile')) {
                return response()->json([
                    'success' => false,
                    'message' => 'File CSV tidak ditemukan'
                ], 422);
            }

            $request->validate([
                'csvFile' => 'required|file|mimes:csv,txt|max:10240'
            ]);

            $file = $request->file('csvFile');

            // Create CSV reader instance
            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setDelimiter(';'); // Set delimiter to semicolon

            // Since there's no header, we'll read records directly
            $records = $csv->getRecords(); // This will treat first row as data

            // Get total records
            $totalRows = iterator_count($csv);
            $csv->setHeaderOffset(0); // Reset the iterator

            $successCount = 0;
            $errors = [];

            DB::beginTransaction();

            try {
                foreach ($records as $rowIndex => $row) {
                    try {
                        // Validate that we have 5 columns
                        if (count($row) !== 5) {
                            throw new \Exception("Baris tidak memiliki 5 kolom yang dibutuhkan");
                        }

                        // Process row - using numeric indices since there's no header
                        $this->processRow([
                            'tanggal' => $row[0],
                            'kode_produk' => $row[1],
                            'type_produk' => $row[2],
                            'jumlah_terjual' => $row[3],
                            'harga_produk' => $row[4]
                        ], $rowIndex + 1);

                        $successCount++;

                        // Commit every 100 rows
                        if ($successCount % 100 === 0) {
                            DB::commit();
                            DB::beginTransaction();
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Row " . ($rowIndex + 1) . ": " . $e->getMessage();
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mengimpor $successCount data dari $totalRows total data",
                    'errors' => $errors
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("CSV Upload Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processRow($row, $rowNumber)
    {
        // Validate tanggal
        $dateValue = trim($row['tanggal']);
        $date = \DateTime::createFromFormat('Y.m.d', $dateValue) ?: // Format 2020.01.01
                \DateTime::createFromFormat('Y-m-d', $dateValue) ?: // Format 2020-01-01
                \DateTime::createFromFormat('d.m.Y', $dateValue); // Format 01.01.2020

        if (!$date) {
            throw new \Exception("Format tanggal tidak valid (contoh format yang valid: 2020.01.01)");
        }

        // Validate kode_produk
        if (empty(trim($row['kode_produk']))) {
            throw new \Exception("Kode Produk tidak boleh kosong");
        }

        // Validate type_produk
        if (empty(trim($row['type_produk']))) {
            throw new \Exception("Type Produk tidak boleh kosong");
        }

        // Validate jumlah_terjual
        $jumlahTerjual = filter_var(trim($row['jumlah_terjual']), FILTER_VALIDATE_INT);
        if ($jumlahTerjual === false || $jumlahTerjual < 0) {
            throw new \Exception("Jumlah terjual harus berupa angka positif");
        }

        // Validate harga_produk
        $hargaProduk = str_replace([',', ' '], '', trim($row['harga_produk']));
        $hargaProduk = filter_var($hargaProduk, FILTER_VALIDATE_FLOAT);
        if ($hargaProduk === false || $hargaProduk < 0) {
            throw new \Exception("Harga produk harus berupa angka positif");
        }

        // Create record
        DataProduct::create([
            'Tanggal' => $date->format('Y-m-d'),
            'Kode_Produk' => trim($row['kode_produk']),
            'Type_Produk' => trim($row['type_produk']),
            'Jumlah_Terjual' => $jumlahTerjual,
            'Harga_Produk' => $hargaProduk
        ]);
    }






}
