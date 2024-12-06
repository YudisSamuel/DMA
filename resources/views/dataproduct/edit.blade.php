<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Produk</title>
    <link rel="stylesheet" href="{{ asset('css/editdata.css') }}">
    @vite(['resources/js/navbar.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
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
                <h2>Edit Data Produk</h2>
                <form action="{{ route('dataproduct.update', $dataProduct->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="tanggal">Tanggal:</label>
                        <input type="date" id="tanggal" name="tanggal" value="{{ $dataProduct->Tanggal }}" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_produk">Kode Produk:</label>
                        <input type="text" id="kode_produk" name="kode_produk" value="{{ $dataProduct->Kode_Produk }}" required>
                    </div>
                    <div class="form-group">
                        <label for="type_produk">Type Produk:</label>
                        <input type="text" id="type_produk" name="type_produk" value="{{ $dataProduct->Type_Produk }}" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_terjual">Jumlah Terjual:</label>
                        <input type="number" id="jumlah_terjual" name="jumlah_terjual" value="{{ $dataProduct->Jumlah_Terjual }}" required>
                    </div>
                    <div class="form-group">
                        <label for="harga_produk">Harga Produk:</label>
                        <input type="number" id="harga_produk" name="harga_produk" value="{{ $dataProduct->Harga_Produk }}" required>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary" onclick="showSuccessAlert()">
                            <i class="fas fa-save"></i> Simpan
                        </button>

                        <button type="button" class="btn btn-danger" onclick="confirmCancel()">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>
</body>
<script>
    function showSuccessAlert() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: true,  // Tombol konfirmasi muncul
            confirmButtonText: 'Tutup',  // Teks tombol
            timer: 5000,  // Durasi 5 detik
            timerProgressBar: true,  // Progress bar
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika tombol ditutup atau notifikasi selesai, redirect ke halaman DataProduct
                window.location.href = '{{ route('DataProduct') }}';
            } else {
                // Jika timer habis, redirect setelah timer selesai
                window.location.href = '{{ route('DataProduct') }}';
            }
        });
    }

    function confirmCancel() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will lose any unsaved changes.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route('DataProduct') }}';
            }
        });
    }
</script>

</html>
