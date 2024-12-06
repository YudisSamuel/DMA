<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @vite(['resources/js/navbar.js', 'resources/js/dashboard.js'])
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
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
                <a href="{{ route('laporan') }}" class="menu-item {{ request()->routeIs('laporan') ? 'active' : '' }}">
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
            <!-- Hapus header disini -->
            <button class="menu-toggle" id="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            {{-- <div class="profile">
                <img src="{{ asset('storage/' . Auth::user()->profile_photos) }}" alt="Profile Picture"
                    style="width: 35px; height: 35px; border-radius: 50%;">
                <div class="dropdown-menu" id="profileDropdown">
                    <a href="{{ route('profile') }}" class="dropdown-item">Pengaturan Profil</a>
                </div>
            </div> --}}
            <div class="profile">
                <i class="bi bi-person-circle" id="profileIcon" style="font-size: 35px; cursor: pointer;"></i>
                <div class="dropdown-menu" id="profileDropdown">
                    <a href="{{ route('profile') }}" class="dropdown-item">Pengaturan Profil</a>
                </div>
            </div>


            {{-- Main content --}}
            <main class="content">
                <!-- Dashboard Content -->
                <div class="dashboard-content">
                    <h2>Dashboard</h2>
                    @if(isset($totalDataProduct))
                    <p>Jumlah data product: {{ $totalDataProduct }}</p>
                @endif
                    <form method="GET" action="{{ route('Home') }}" id="year-filter-form">
                        <form method="GET" action="{{ route('Home') }}" id="year-filter-form">
                            <label for="year">Pilih Tahun:</label>
                            <select name="year" id="year" onchange="document.getElementById('year-filter-form').submit();">
                                <option value="2025" {{ request('year') == 2025 ? 'selected' : '' }}>2025</option>
                                <option value="2024" {{ request('year') == 2024 || !request()->has('year') ? 'selected' : '' }}>2024</option>
                                <option value="2023" {{ request('year') == 2023 ? 'selected' : '' }}>2023</option>
                                <option value="2022" {{ request('year') == 2022 ? 'selected' : '' }}>2022</option>
                            </select>
                        </form>
                    </form>

                             <!-- KPI Cards -->
                    <div class="kpi-cards">
                        <div class="card card-blue">
                            <p class="amount">Total Penjualan</p>
                            <p class="total-amount">{{ $totalPenjualan }} Unit</p>
                        </div>
                        <div class="card card-green">
                            <p class="amount">Peramalan Bulan Terakhir</p>
                            @if(isset($latestPrediction))
                                <div class="prediction-details">
                                    <p class="prediction-month">{{ $latestPrediction->Bulan }}</p>
                                    <div class="separator">|</div>
                                    <p class="prediction-month">{{ $latestPrediction->Type_Produk }}</p>
                                    <div class="separator">|</div>
                                    <p class="prediction-value">{{ number_format($latestPrediction->Prediksi, 0) }} Unit</p>
                                </div>
                            @else
                                <p class="no-prediction">Belum ada peramalan</p>
                            @endif
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="charts">
                        <div class="chart-container1">
                            <h3>Grafik Penjualan</h3>
                            <canvas id="earningsChart"></canvas>
                        </div>
                        <div id="penjualanBulanan" style="display: none;">
                            @json($penjualanBulanan)
                        </div>

                        <div class="chart-container2">
                            <h3>Grafik Keuntungan</h3>
                            <h4>Total Keuntungan: Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}</h4>
                            <!-- Label total keuntungan -->
                            <canvas id="revenueChart"></canvas>
                        </div>

                        <div id="dataKeuntungan" style="display: none;">
                            @json($keuntungan)
                        </div>

                    </div>
                </div>
            </main>
        </main>
    </div>
</body>

</html>
