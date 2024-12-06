<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataProduct;
use App\Models\Peramalan;

class DmaPredictionController extends Controller
{
    public function index()
    {
        $products = DataProduct::select('Type_Produk')->distinct()->get();
        return view('dma_prediction', compact('products'));
    }

    public function getMonthlySales(Request $request)
    {
        try {
            $productType = $request->input('product-type');
            $period = $request->input('period', 3);
            $forecastPeriod = $request->input('forecast-period', 1);

            // Log untuk debugging
            \Log::info('Product Type: ' . $productType);
            \Log::info('Period: ' . $period);
            \Log::info('Forecast Period: ' . $forecastPeriod);

            // Query data
            $query = DB::table('tb_DataProduct')
                ->select(
                    DB::raw("DATE_FORMAT(Tanggal, '%Y-%m') as month"),
                    DB::raw('SUM(Jumlah_Terjual) as total_sales')
                );

            if ($productType !== 'Semua Tipe Produk') {
                $query->where('Type_Produk', $productType);
            }

            $salesData = $query->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            $formattedData = [];
            foreach ($salesData as $data) {
                $formattedData[] = [
                    'month' => $data->month,
                    'total_sales' => (float)$data->total_sales
                ];
            }

            // Validasi jumlah data
            if (count($formattedData) < $period) {
                return response()->json([
                    'error' => "Jumlah data kurang dari periode yang ditentukan. Hanya ada " . count($formattedData) . " data."
                ], 400);
            }

            $result = $this->calculateDMA($formattedData, $period, $forecastPeriod);

            return response()->json([
                'sales_data' => $formattedData,
                'forecast' => $result
            ]);

        } catch (\Exception $e) {
            // Log error
            \Log::error('Error in getMonthlySales: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    private function calculateDMA($data, $period, $forecastPeriod)
    {
        if (empty($data) || !is_array($data)) {
            return ['error' => 'Data input tidak valid.'];
        }

        // 1. Ambil data penjualan
        $sales = array_map(function($item) {
            return is_numeric($item['total_sales']) ? (float)$item['total_sales'] : null;
        }, $data);

        // 2. Validasi jumlah data
        if (count(array_filter($sales)) < ($period * 2)) {
            return ['error' => 'Data tidak cukup untuk perhitungan DMA. Minimal ' . ($period * 2) . ' data point.'];
        }

        // 3. Hitung SMA pertama (SMA1)
        $sma1 = [];
        for ($i = $period - 1; $i < count($sales); $i++) {
            $subset = array_slice($sales, $i - $period + 1, $period);
            $subset = array_filter($subset); // Buang nilai null
            $sma1[] = count($subset) > 0 ? array_sum($subset) / count($subset) : null;
        }

        // 4. Hitung SMA kedua (SMA2 atau DMA)
        $sma2 = [];
        for ($i = $period - 1; $i < count($sma1); $i++) {
            $subset = array_slice($sma1, $i - $period + 1, $period);
            $subset = array_filter($subset); // Buang nilai null
            $sma2[] = count($subset) > 0 ? array_sum($subset) / count($subset) : null;
        }

        // 5. Geser indeks SMA dan DMA agar sesuai dengan periode
        $sma = array_fill(0, $period - 1, null); // Isi null hingga SMA pertama muncul
        $sma = array_merge($sma, array_map(function($val) { return is_null($val) ? null : round($val, 2); }, $sma1)); // Gabungkan SMA1

        $dma = array_fill(0, $period * 2 - 2, null); // Isi null hingga DMA pertama muncul
        $dma = array_merge($dma, array_map(function($val) { return is_null($val) ? null : round($val, 2); }, $sma2)); // Gabungkan SMA2 menjadi DMA

        // 6. Hitung At (2 * SMA1 - DMA)
        $at = array_fill(0, count($sales), 0); // Inisialisasi At dengan null di awal
        for ($i = ($period * 2 - 2); $i < count($dma); $i++) {
            if (isset($sma1[$i - ($period - 1)]) && isset($dma[$i]) && !is_null($sma1[$i - ($period - 1)]) && !is_null($dma[$i])) {
                $at[$i] = round(2 * $sma1[$i - ($period - 1)] - $dma[$i], 2);
            }
        }

        // 7. Hitung Bt
        $bt = array_fill(0, count($sales), 0); // Inisialisasi Bt dengan null di awal
        for ($i = ($period * 2 - 2); $i < count($dma); $i++) {
            if (isset($sma1[$i - ($period - 1)]) && isset($dma[$i]) && !is_null($sma1[$i - ($period - 1)]) && !is_null($dma[$i])) {
                $bt[$i] = round((2 / ($period - 1)) * ($sma1[$i - ($period - 1)] - $dma[$i]), 2);
            }
        }

        // 8. Hitung forecast untuk setiap periode
        $forecasts = [];
        for ($i = ($period * 2 - 1); $i < count($sales) + 1; $i++) {
            // Indeks untuk At dan Bt (harus sesuai dengan data tersedia)
            $atIndex = $i - 1;
            $btIndex = $i - 1;

            if (isset($at[$atIndex]) && isset($bt[$btIndex]) && !is_null($at[$atIndex]) && !is_null($bt[$btIndex])) {
                $forecasts[$i] = round($at[$atIndex] + ($bt[$btIndex] * $forecastPeriod), 2);
            } else {
                $forecasts[$i] = null; // Jika data tidak tersedia
            }
        }
        $forecast_next_period = isset($at[count($sales) - 1]) && isset($bt[count($sales) - 1])
        ? round($at[count($sales) - 1] + ($bt[count($sales) - 1] * $forecastPeriod), 2)
        : 0;

        // 9. Hitung MAPE
        $totalAPE = 0;
        $validCount = 0;
        for ($i = ($period * 2 - 1); $i < count($sales); $i++) {
            $actual = $sales[$i];
            $predicted = $forecasts[$i + 1] ?? null; // Prediksi untuk baris di bawah At dan Bt
            if (!is_null($actual) && !is_null($predicted) && $actual != 0) {
                $ape = abs(($actual - $predicted) / $actual) * 100;
                $totalAPE += $ape;
                $validCount++;
            }
        }
        $mapeFinal = $validCount > 0 ? ($totalAPE / $validCount) : 0;



        return [
            'current_value' => end($sales) ?: 0,
            'forecast_next_period' => $forecast_next_period,
            'forecasts' => $forecasts,
            'sma1' => $sma,
            'sma2' => $dma,
            'dma' => $dma,
            'a' => $at,
            'b' => $bt,
            'mape' => round($mapeFinal, 2)
        ];


    }


    public function saveForecastToDatabase(Request $request) {
        try {
            // Validate request data
            $validated = $request->validate([
                'month' => 'required|string|max:7', // Format: YYYY-MM
                'product_type' => 'required|string|max:255',
                'forecast_next_period' => 'required|numeric',
                'mape' => 'required|numeric'
            ]);

            // Check if forecast already exists for this month and product
            $existingForecast = Peramalan::where('Bulan', $validated['month'])
                ->where('Type_Produk', $validated['product_type'])
                ->first();

            if ($existingForecast) {
                // Update existing forecast
                $existingForecast->update([
                    'Prediksi' => $validated['forecast_next_period'],
                    'mape' => $validated['mape']
                ]);
            } else {
                // Create new forecast
                Peramalan::create([
                    'Bulan' => $validated['month'],
                    'Type_Produk' => $validated['product_type'],
                    'Prediksi' => $validated['forecast_next_period'],
                    'mape' => $validated['mape']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Hasil peramalan berhasil disimpan.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving forecast: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan peramalan: ' . $e->getMessage()
            ], 500);
        }
    }




}
