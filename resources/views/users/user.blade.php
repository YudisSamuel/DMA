<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    @vite(['resources/js/navbar.js','resources/js/user.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
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

            <!-- Konten -->
            <div class="content">
                {{-- <div class="filter-container">
                    <button id="addUser" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">+ Tambah User</button>
                </div> --}}

                <!-- Tabel Data User -->
                <div class="content-wrapper">
                    <h2>Daftar Pengguna</h2>
                    @if (session('success'))
                        <p>{{ session('success') }}</p>
                    @endif
                    <div class="table-container">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Foto</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->username }}</td>
                                        <td>
                                            @if ($user->profile_photo)
                                                <img src="{{ $user->profile_photo_url }}"
                                                     alt="Foto Profil"
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                            @else
                                                <img src="{{ asset('path/to/default/profile-image.png') }}"
                                                     alt="Foto Profil Default"
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('user.edit', $user->id_pgn) }}" class="btn btn-edit">Edit</a>
                                            <form action="{{ route('user.destroy', $user->id_pgn) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus?')">Delete</button>
                                            </form>
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

    <!-- Modal untuk Tambah User -->
    {{-- <div class="modal" id="addUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="foto">Foto:</label>
                            <input type="file" name="foto" id="foto" class="form-control" accept="image/*" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div> --}}
</body>

</html>
