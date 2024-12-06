<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Peramalan</title>
    <link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
    @vite(['resources/js/navbar.js', 'resources/js/product-filter.js', 'resources/js/laporan.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            <!-- Top Navigation -->
            <button class="menu-toggle" id="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="profile">
                <i class="bi bi-person-circle" id="profileIcon" style="font-size: 35px; cursor: pointer;"></i>
                <div class="dropdown-menu" id="profileDropdown">
                    <a href="{{ route('profile') }}" class="dropdown-item">Pengaturan Profil</a>
                </div>
            </div>

            <!-- Form Data -->
            <div class="content">
                @if(session('success'))
                <script>
                    Swal.fire({
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true
                    });
                </script>
                @endif

                <div class="filter-container">
                    <form method="GET" action="{{ route('laporan') }}" class="filter-form">
                        <select id="typeFilter" name="typeFilter" class="filter-select" onchange="this.form.submit()">
                            <option value="">Semua Type Produk</option>
                            @foreach ($uniqueTypes as $type)
                                <option value="{{ $type }}" {{ $type == $typeFilter ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <form id="clearForm" method="POST">
                        @csrf
                        <button type="submit" id="clearButton">Clear Histories</button>
                    </form>

                    <form method="POST" action="{{ route('laporan.restore') }}" class="restore-form">
                        @csrf
                        <button type="submit" class="btn btn-primary">Pulihkan Data</button>
                    </form>


                </div>

                <!-- Tabel Data Penjualan -->
                <div class="content-wrapper">
                    <div class="table-container">
                        <table class="styled-table" id="laporanTable">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Tipe produk</th>
                                    <th>Peramalan</th>
                                    <th>MAPE</th>
                                    <th>Action</th> <!-- Kolom Action baru -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($peramalanData as $data)
                                <tr>
                                    <td>{{ $data->Bulan }}</td>
                                    <td>{{ $data->Type_Produk }}</td>
                                    <td>{{ $data->Prediksi }}</td>
                                    <td>{{ $data->mape }}</td>
                                    <td>
                                        <a href="{{ route('laporan.cetak', $data->id) }}" class="print-button">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>

</html>
