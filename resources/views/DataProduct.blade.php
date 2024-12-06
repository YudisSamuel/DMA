<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
    <link rel="stylesheet" href="{{ asset('css/dataproduct.css') }}">
    @vite(['resources/js/navbar.js', 'resources/js/dashboard.js', 'resources/js/product-filter.js', 'resources/js/ceklist-kolom.js', 'resources/js/nextpage.js'])
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            <button class="menu-toggle" id="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="profile">
                <i class="bi bi-person-circle" id="profileIcon" style="font-size: 35px; cursor: pointer;"></i>
                <div class="dropdown-menu" id="profileDropdown">
                    <a href="{{ route('profile') }}" class="dropdown-item">Pengaturan Profil</a>
                </div>
            </div>

            {{-- Main content --}}
            <div class="content">
                <div class="filter-container">
                    <button id="addDataProduct" class="btn btn-primary">+ ADD Data Product</button>
                    <div class="upload-container">
                        <button id="uploadButton" class="btn btn-primary">Upload CSV</button>
                        <form id="uploadCsvForm" action="{{ route('dataproduct.upload') }}" method="POST"
                            enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="file" id="csvFile" name="csvFile" accept=".csv">
                        </form>
                    </div>
                    <select id="typeFilter" class="filter-select">
                        <option value="">Semua Type Produk</option>
                        @php
                            $uniqueTypes = $DataProduct->pluck('Type_Produk')->unique();
                        @endphp
                        @foreach ($uniqueTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>

                    <div class="rows-per-page">
                        <label for="rowsPerPage">Tampilkan:</label>
                        <select id="rowsPerPage" class="filter-select">
                            <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                            <option value="500" {{ request('perPage') == 500 ? 'selected' : '' }}>500</option>
                        </select>
                        <span>entries</span>
                    </div>
                </div>
                {{-- form /tabel data penjualan --}}
                <div class="content-wrapper">
                    <div class="table-container">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode Produk</th>
                                    <th>Type Produk</th>
                                    <th>Jumlah Terjual</th>
                                    <th>Harga Produk</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="DataProductTableBody">
                                @foreach ($DataProduct as $product)
                                    <tr data-id="{{ $product->id }}" data-type="{{ $product->Type_Produk }}">
                                        <td>{{ $product->Tanggal }}</td>
                                        <td>{{ $product->Kode_Produk }}</td>
                                        <td>{{ $product->Type_Produk }}</td>
                                        <td>{{ $product->Jumlah_Terjual }}</td>
                                        <td>{{ $product->Harga_Produk }}</td>
                                        <td class="action-buttons">
                                            <button class="action-btn edit-btn"
                                                onclick="window.location.href='{{ route('dataproduct.edit', $product->id) }}'">Edit</button>
                                            <button class="action-btn view-btn"
                                                onclick="window.location.href='{{ route('dataproduct.show', $product->id) }}'">View</button>
                                            <form action="{{ route('dataproduct.destroy', $product->id) }}"
                                                method="POST" style="display:inline;"
                                                id="delete-form-{{ $product->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-btn delete-btn"
                                                    id="delete-btn-{{ $product->id }}">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination Controls -->
                        <div class="pagination-controls">
                            <div class="pagination-info">
                                Showing
                                <span id="startEntry">{{ $DataProduct->firstItem() }}</span>
                                to
                                <span id="endEntry">{{ $DataProduct->lastItem() }}</span>
                                of
                                <span id="totalEntries">{{ $DataProduct->total() }}</span> entries
                            </div>
                            <div class="pagination-buttons">
                                <button id="firstPage" class="pagination-btn"
                                    {{ $DataProduct->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-angle-double-left"></i>
                                </button>
                                <button id="prevPage" class="pagination-btn"
                                    {{ $DataProduct->currentPage() <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-angle-left"></i>
                                </button>
                                <span id="currentPage">
                                    Page {{ $DataProduct->currentPage() }} of {{ $DataProduct->lastPage() }}
                                </span>
                                <button id="nextPage" class="pagination-btn"
                                    {{ $DataProduct->currentPage() >= $DataProduct->lastPage() ? 'disabled' : '' }}>
                                    <i class="fas fa-angle-right"></i>
                                </button>
                                <button id="lastPage" class="pagination-btn"
                                    {{ $DataProduct->currentPage() >= $DataProduct->lastPage() ? 'disabled' : '' }}>
                                    <i class="fas fa-angle-double-right"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                {{-- FORM INOUT DATA --}}
                <div id="DataProductModal" class="modal" aria-hidden="true">
                    <div class="modal-content">
                        <h2 id="modalTitle">Add DataProduct</h2>
                        <form id="DataProductForm" action="{{ route('dataproduct.store') }}" method="POST">
                            @csrf
                            <input type="hidden" id="DataProductId" name="id">
                            <div class="form-group">
                                <label for="tanggal">Tanggal:</label>
                                <input type="date" id="tanggal" name="tanggal" required>
                            </div>
                            <div class="form-group">
                                <label for="kode_produk">Kode Produk:</label>
                                <input type="text" id="kode_produk" name="kode_produk" required>
                            </div>
                            <div class="form-group">
                                <label for="type_produk">Type Produk:</label>
                                <input type="text" id="type_produk" name="type_produk" required>
                            </div>
                            <div class="form-group">
                                <label for="jumlah_terjual">Jumlah Terjual:</label>
                                <input type="number" id="jumlah_terjual" name="jumlah_terjual" required>
                            </div>
                            <div class="form-group">
                                <label for="harga_produk">Harga Produk:</label>
                                <input type="number" id="harga_produk" name="harga_produk" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" onclick="closeModal()">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('DataProductForm');
        const saveButton = form.querySelector('button[type="submit"]'); // Tombol submit dalam form

        saveButton.addEventListener('click', function(e) {
            e.preventDefault(); // Cegah form langsung terkirim

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyimpan data produk ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mengirim form menggunakan fetch
                    const formData = new FormData(form);
                    fetch(form.action, {
                            method: form.method,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => {
                            console.log('Response Status:', response.status); // Log status
                            return response.text(); // Ambil respons sebagai teks
                        })
                        .then(data => {
                            console.log('Response Data:', data); // Log isi respons

                            try {
                                const parsedData = JSON.parse(
                                    data); // Coba parsing jika data JSON
                                if (parsedData.success) {
                                    Swal.fire('Sukses!',
                                            'Data berhasil diunggah dan diproses!',
                                            'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Gagal!', parsedData.message ||
                                        'Terjadi kesalahan saat memproses file.',
                                        'error');
                                }
                            } catch (error) {
                                console.error('Parsing Error:',
                                    error); // Cek jika ada error saat parsing JSON
                                Swal.fire('Error!',
                                    'Terjadi kesalahan saat memproses file.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Fetch Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                        });

                }
            });
        });

        // Fungsi untuk menutup modal (menyembunyikan elemen modal)
        function closeModal() {
            const modal = document.getElementById('DataProductModal');
            modal.style.display = 'none'; // Menyembunyikan modal
        }
    });

    // Pastikan kode ini berada dalam sebuah loop atau area yang memiliki akses ke variabel $product
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach((deleteBtn) => {
            const formId = deleteBtn.getAttribute('id').replace('delete-btn-', '');
            const deleteForm = document.getElementById(`delete-form-${formId}`);

            if (deleteForm) {
                deleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Kirim permintaan menggunakan Fetch API
                            fetch(deleteForm.action, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                })
                                .then(response => response
                            .json()) // Parsing respons ke JSON
                                .then(data => {
                                    if (data.success) {
                                        // Notifikasi sukses
                                        Swal.fire('Deleted!', data.message,
                                            'success').then(() => {
                                            // Redirect atau reload halaman
                                            window.location.reload();
                                        });
                                    } else {
                                        // Notifikasi gagal
                                        Swal.fire('Error!', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    // Tangani kesalahan jaringan
                                    Swal.fire('Error!',
                                        'Terjadi kesalahan saat menghapus data.',
                                        'error');
                                    console.error(error);
                                });
                        }
                    });
                });
            }
        });
    });



    // upload csv
    document.addEventListener('DOMContentLoaded', function() {
        const uploadButton = document.getElementById('uploadButton');
        const csvFileInput = document.getElementById('csvFile');
        const uploadForm = document.getElementById('uploadCsvForm');

        uploadButton.addEventListener('click', function() {
            csvFileInput.click();
        });

        csvFileInput.addEventListener('change', async function() {
            const file = this.files[0];

            if (!file) {
                Swal.fire('Error!', 'Silakan pilih file CSV!', 'error');
                return;
            }

            // Validate file extension and size
            const fileExt = file.name.split('.').pop().toLowerCase();
            if (fileExt !== 'csv') {
                Swal.fire('Error!', 'File harus berformat CSV!', 'error');
                this.value = '';
                return;
            }

            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                Swal.fire('Error!', 'Ukuran file tidak boleh lebih dari 10MB!', 'error');
                this.value = '';
                return;
            }

            try {
                const result = await Swal.fire({
                    title: 'Konfirmasi Upload',
                    text: `Apakah Anda yakin ingin mengunggah file ${file.name}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, unggah',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                });

                if (!result.isConfirmed) {
                    this.value = '';
                    return;
                }

                const formData = new FormData(uploadForm);

                // Show loading with progress bar
                const loadingSwal = Swal.fire({
                    title: 'Mengupload...',
                    html: `
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            0%
                        </div>
                    </div>
                `,
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                const response = await fetch(uploadForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        title: 'Berhasil!',
                        html: `${data.message}<br><br>${data.errors.length > 0 ?
                          '<small class="text-warning">Beberapa baris memiliki error:</small><br>' +
                          data.errors.join('<br>') : ''}`,
                        icon: 'success'
                    });
                    location.reload();
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat upload file');
                }

            } catch (error) {
                console.error('Upload error:', error);
                await Swal.fire({
                    title: 'Error!',
                    text: `Terjadi kesalahan: ${error.message}`,
                    icon: 'error'
                });
            } finally {
                this.value = '';
            }
        });
    });
</script>

</html>
