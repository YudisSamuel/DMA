function updateInterpretation(data, productType) {
    const interpretationDiv = document.getElementById("interpretation-result");

    // Pastikan div interpretasi tersedia
    if (!interpretationDiv) {
        console.error("Elemen interpretation-result tidak ditemukan!");
        return;
    }

    // Ambil nilai prediksi dan MAPE dari data terakhir
    const forecastNextPeriod = data.forecast.forecast_next_period || 0;
    const mapeFinal = document.querySelector("#forecast-table tbody tr:last-child td:last-child").textContent;

    // Parse MAPE value (remove % sign)
    const mape = parseFloat(mapeFinal);

    // Tentukan deskripsi akurasi
    let accuracyDescription = "perlu peningkatan akurasi";
    if (mape < 10) {
        accuracyDescription = "sangat baik";
    } else if (mape <= 20) {
        accuracyDescription = "baik";
    } else if (mape <= 50) {
        accuracyDescription = "cukup baik";
    }

    // Formatter untuk angka
    const formatter = new Intl.NumberFormat('id-ID');

    // Buat HTML interpretasi
    const interpretationHTML = `
        <div class="interpretasi-table">
            <table>
                <tr>
                    <th>Produk yang Diramalkan</th>
                    <td><strong>${productType}</strong></td>
                </tr>
                <tr>
                    <th>Nilai Peramalan Menunjukkan Prediksi Sebesar</th>
                    <td><strong>${formatter.format(forecastNextPeriod)}</strong></td>
                </tr>
                <tr>
                    <th>Tingkat Akurasi Peramalan (MAPE) Sebesar</th>
                    <td><strong>${mapeFinal}</strong></td>
                    <th>Yang Menunjukkan Tingkat Akurasi</th>
                    <td><strong>${accuracyDescription}</strong></td>
                </tr>
            </table>
        </div>
    `;

    // Masukkan HTML ke div interpretasi
    interpretationDiv.innerHTML = interpretationHTML;
}
// Modify the existing event listener to call updateInterpretation
document
    .getElementById("dma-form")
    .addEventListener("submit", function (event) {
        event.preventDefault();

        const productType = document.getElementById("product-type").value;
        const period = document.getElementById("dma-period").value;
        const forecastPeriod = document.getElementById("forecast-period").value;

        // Validasi input
        if (!productType || !period || !forecastPeriod) {
            Swal.fire({
                icon: "warning",
                title: "Peringatan",
                text: "Harap isi semua field yang diperlukan!",
                confirmButtonColor: "#f39c12",
            });
            return;
        }

        const resultDiv = document.getElementById("prediction-result");
        resultDiv.innerHTML = "<p>Sedang memuat...</p>";

        // Gunakan fetch dengan method POST
        fetch("/dma-prediction", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({
                "product-type": productType,
                period: period,
                "forecast-period": forecastPeriod,
            }),
        })
            .then((response) => {
                // Cek status response
                if (!response.ok) {
                    // Coba parsing error dari response JSON
                    return response.json().then((errData) => {
                        throw new Error(
                            errData.error || "Terjadi kesalahan tidak dikenal"
                        );
                    });
                }
                return response.json();
            })
            .then((data) => {
                // Cek apakah ada error di dalam data
                if (data.error) {
                    throw new Error(data.error);
                }
                displayResults(data);
            })
            .catch((error) => {
                console.error("Error:", error);
                resultDiv.innerHTML = `<p>Terjadi kesalahan: ${error.message}</p>`;

                Swal.fire({
                    icon: "error",
                    title: "Kesalahan",
                    text: error.message,
                    confirmButtonColor: "#d33",
                });
            });
    });

// Update the displayResults function to store the forecast data globally
let currentForecastData = null;

function displayResults(data) {
    currentForecastData = data;

    const resultDiv = document.getElementById("prediction-result");
    const formatter = new Intl.NumberFormat("id-ID");

    // Kosongkan hasil sebelumnya
    resultDiv.innerHTML = "";

    // Tampilkan data penjualan bulanan
    const forecastTableBody = document.querySelector("#forecast-table tbody");
    forecastTableBody.innerHTML = "";

    const period = 3; // Periode 3 bulan (default)

    // Variabel untuk menyimpan total APE dan jumlah data valid
    let totalAPE = 0;
    let validCount = 0;

    // Iterasi untuk setiap data penjualan dan perhitungan APE
    data.sales_data.forEach((item, index) => {
        let sma = "-",
            dma = "-",
            at = "-",
            bt = "-",
            prediksi = "-",
            mape = "-";

        if (data.forecast) {
            // Ambil nilai SMA dan DMA jika tersedia
            sma =
                data.forecast.sma1[index] !== null &&
                index < data.forecast.sma1.length
                    ? data.forecast.sma1[index]
                    : "-";
            dma =
                data.forecast.sma2[index] !== null &&
                index < data.forecast.sma2.length
                    ? data.forecast.sma2[index]
                    : "-";

            // Ambil nilai At dan Bt
            at =
                index >= period * 2 - 2 && data.forecast.a[index] !== undefined
                    ? parseFloat(data.forecast.a[index])
                    : 0;
            bt =
                index >= period * 2 - 2 && data.forecast.b[index] !== undefined
                    ? parseFloat(data.forecast.b[index])
                    : 0;

            // Ambil prediksi langsung dari server
            prediksi =
                index >= period * 2 - 2 &&
                data.forecast.forecasts[index] !== undefined
                    ? data.forecast.forecasts[index].toFixed(2)
                    : "-";

            // Perhitungan APE dan MAPE
            if (prediksi !== "-" && item.total_sales) {
                const actual = parseFloat(item.total_sales);
                const forecast = parseFloat(prediksi);
                if (actual !== 0 && forecast !== 0) {
                    mape = (
                        (Math.abs(actual - forecast) / actual) *
                        100
                    ).toFixed(2);
                    if (index >= period * 2 - 2) {
                        totalAPE += parseFloat(mape);
                        validCount++;
                    }
                }
            }
        }

        // Tambahkan baris ke tabel
        const row = `
    <tr>
        <td>${formatMonth(item.month)}</td>
        <td>${formatter.format(item.total_sales)}</td>
        <td>${sma !== "-" ? formatter.format(sma) : "-"}</td>
        <td>${dma !== "-" ? formatter.format(dma) : "-"}</td>
        <td>${at !== 0 ? at.toFixed(2) : "-"}</td>
        <td>${bt !== 0 ? bt.toFixed(2) : "-"}</td>
        <td>${prediksi !== "-" ? formatter.format(prediksi) : "-"}</td>
        <td>${mape !== "-" ? mape + "%" : "-"}</td>
    </tr>
    `;
        forecastTableBody.innerHTML += row;
    });

    // Validasi nilai forecast_next_period
    const forecastNextPeriod = data.forecast.forecast_next_period
        ? formatter.format(data.forecast.forecast_next_period)
        : "0";

    // Hitung MAPE final
    const mapeFinal =
        validCount > 0 ? (totalAPE / validCount).toFixed(2) : "0.00";

    // Tambahkan baris untuk Prediksi Periode Berikutnya
    const lastRow = `
<tr>
    <td>Peramalan</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td><strong>${forecastNextPeriod}</strong></td>
    <td><strong>${mapeFinal}%</strong></td>
</tr>
`;
    forecastTableBody.innerHTML += lastRow;
    updateInterpretation(data, document.getElementById("product-type").value);
}

function formatMonth(monthStr) {
    const date = new Date(monthStr + "-01");
    return date.toLocaleDateString("id-ID", { year: "numeric", month: "long" });
}

document.addEventListener("DOMContentLoaded", function () {
    // Add click event listener for save button
    document
        .getElementById("save-button")
        .addEventListener("click", function () {
            if (!currentForecastData) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Harap lakukan peramalan terlebih dahulu",
                    confirmButtonColor: "#d33",
                });
                return;
            }

            try {
                // Get the selected product type
                const productType =
                    document.getElementById("product-type").value;

                // Get the forecast period from the input field
                const forecastPeriod = parseInt(
                    document.getElementById("forecast-period").value
                );

                if (isNaN(forecastPeriod) || forecastPeriod < 1) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Nilai periode ramalan tidak valid",
                        confirmButtonColor: "#d33",
                    });
                    return;
                }

                // Get the latest month from sales data
                const salesData = currentForecastData.sales_data;
                const latestMonth = salesData[salesData.length - 1].month;

                // Calculate the forecast month based on forecastPeriod
                const nextMonthDate = new Date(latestMonth + "-01");
                nextMonthDate.setMonth(nextMonthDate.getMonth() + forecastPeriod);
                const forecastMonth = nextMonthDate.toISOString().slice(0, 7);

                // Get MAPE from the table's last row
                const mapeCell = document.querySelector(
                    "#forecast-table tbody tr:last-child td:last-child"
                );
                const mapeValue = parseFloat(
                    mapeCell.textContent.replace("%", "")
                );

                // Prepare data for saving
                const forecastData = {
                    month: forecastMonth,
                    product_type: productType,
                    forecast_next_period:
                        currentForecastData.forecast.forecast_next_period,
                    mape: mapeValue,
                };

                // Send data to server
                fetch("/save-forecast", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify(forecastData),
                })
                    .then((response) => response.json())
                    .then((result) => {
                        if (result.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil",
                                text: "Hasil peramalan berhasil disimpan!",
                                confirmButtonColor: "#3085d6",
                            });
                        } else {
                            throw new Error(
                                result.message ||
                                    "Gagal menyimpan hasil peramalan"
                            );
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Terjadi kesalahan: " + error.message,
                            confirmButtonColor: "#d33",
                        });
                    });
            } catch (error) {
                console.error("Error:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Terjadi kesalahan saat menyimpan data",
                    confirmButtonColor: "#d33",
                });
            }
        });
});

