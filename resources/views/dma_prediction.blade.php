    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>DMA Prediction</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        {{-- <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('css/dma-prediction.css') }}">
        @vite(['resources/js/navbar.js', 'resources/js/dashboard.js', 'resources/js/dma-prediction.js'])
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dma-prediction.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    </head>

    <body>
        <div class="dashboard">
            <!-- Sidebar -->
            <nav class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <div class="logo-container">
                        <i class="bi bi-puzzle-fill"></i>
                        <span class="logo-text">JIGZLE</span>
                    </div>
                </div>
                <div class="menu-items">
                    <a href="{{ route('Home') }}" class="menu-item {{ request()->routeIs('Home') ? 'active' : '' }}">
                        <i class="fas fa-th"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                    <a href="{{ route('DataProduct') }}"
                        class="menu-item {{ request()->routeIs('DataProduct') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span class="menu-text">Data Produk</span>
                    </a>
                    <a href="{{ route('dma_prediction') }}"
                        class="menu-item {{ request()->routeIs('dma_prediction') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span class="menu-text">Peramalan</span>
                    </a>
                    <a href="{{ route('laporan') }}"
                        class="menu-item {{ request()->routeIs('laporan') ? 'active' : '' }}">
                        <i class="fas fa-folder"></i>
                        <span class="menu-text">Laporan</span>
                    </a>
                </div>

                <div class="bottom-item">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="menu-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="menu-text">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Top Navigation (sama seperti sebelumnya) -->
                <button class="menu-toggle" id="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="profile">
                    <i class="bi bi-person-circle" id="profileIcon" style="font-size: 35px; cursor: pointer;"></i>
                    <div class="dropdown-menu" id="profileDropdown">
                        <a href="{{ route('profile') }}" class="dropdown-item">Pengaturan Profil</a>
                    </div>
                </div>

                <!-- DMA Prediction Content -->
                <div class="dma-prediction-content">
                    <h2>Peramalan dengan Double Moving Average</h2>

                    <div class="prediction-container">
                        <div class="input-section">
                            <h3>Input Data</h3>
                            <form id="dma-form">
                                <div class="form-group">
                                    <label for="product-type">Pilih Produk:</label>
                                    <select id="product-type" name="product-type" required>
                                        <option value="">Pilih Produk</option>
                                        <option value="Semua Tipe Produk">Semua Produk</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->Type_Produk }}">{{ $product->Type_Produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dma-period">Pilih Nilai N untuk menghitung rata-rata bergerak</label>
                                    <input type="number" id="dma-period" name="dma-period" min="1" value="3" >
                                </div>
                                <div class="form-group">
                                    <label for="forecast-period">Pilih Nilai m Untuk Menghitung Periode Kedepan Yang Diramalkan</label>
                                    <input type="number" id="forecast-period" name="forecast_period" min="1" max="12" class="form-control" required>
                                </div>
                                <button id="predict-button">Predict</button>
                            </form>
                        </div>
                    </div>
                    <div class="prediction-container">
                        <div class="output-section">
                            <h3>Hasil Peramalan</h3>
                            <div id="prediction-result"></div>
                            <div id="interpretation-result"></div>
                            <button id="save-button">Save Peramalan</button>
                            <div class="table-responsive pt-3">
                                <table class="table table-bordered" id="forecast-table">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Penjualan</th>
                                            <th>SMA</th>
                                            <th>DMA</th>
                                            <th>At</th>
                                            <th>Bt</th>
                                            <th>Prediksi</th>
                                            <th>MAPE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan diisi oleh JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
            </main>
        </div>

    </body>

    </html>
