<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Data Product</title>
    <link rel="stylesheet" href="{{ asset('css/viewdata.css') }}">
    @vite(['resources/js/navbar.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                <a href="{{ route('DataProduct') }}" class="menu-item {{ request()->routeIs('DataProduct') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span class="menu-text">Data Produk</span>
                </a>
                <a href="{{ route('dma_prediction') }}" class="menu-item {{ request()->routeIs('dma_prediction') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span class="menu-text">Peramalan</span>
                </a>
                <a href="{{ route('laporan') }}" class="menu-item {{ request()->routeIs('laporan') ? 'active' : '' }}">
                    <i class="fas fa-folder"></i>
                    <span class="menu-text">Laporan</span>
                </a>
            </div>

            <div class="bottom-item">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="menu-item">
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
            <!-- Form Edit Data -->
            <div class="container">
                <h2>Detail Data Produk</h2>

                <div class="form-group">
                    <label for="tanggal">Tanggal:</label>
                    <p>{{ $product->Tanggal }}</p>
                </div>
                <div class="form-group">
                    <label for="kode_produk">Kode Produk:</label>
                    <p>{{ $product->Kode_Produk }}</p>
                </div>
                <div class="form-group">
                    <label for="type_produk">Type Produk:</label>
                    <p>{{ $product->Type_Produk }}</p>
                </div>
                <div class="form-group">
                    <label for="jumlah_terjual">Jumlah Terjual:</label>
                    <p>{{ $product->Jumlah_Terjual }}</p>
                </div>
                <div class="form-group">
                    <label for="harga_produk">Harga Produk:</label>
                    <p>{{ $product->Harga_Produk }}</p>
                </div>

                <button class="btn btn-primary" onclick="window.location.href='{{ route('dataproduct.edit', $product->id) }}'">Edit</button>
                <button class="btn btn-danger" onclick="window.location.href='{{ route('DataProduct') }}'">Kembali</button>
            </div>

        </main>
    </div>
</body>

</html>


