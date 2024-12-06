<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Profile</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <button onclick="window.location.href='Home'" class="back-button">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5M12 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </button>

    <div class="profile-container">
        <h2>Profile</h2>

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: '{{ session("success") }}',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/Home';
                        }
                    });
                });
            </script>
        @endif

        @if($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Error!',
                        html: `@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
        @endif

        <div class="profile-card">
            <form class="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="name" value="{{ $user->username }}" required>
                </div>

                <div class="form-group">
                    <label for="old-password">Password Lama</label>
                    <input type="password" id="old-password" name="old_password">
                </div>

                <div class="form-group">
                    <label for="new-password">Password Baru</label>
                    <input type="password" id="new-password" name="new_password" pattern=".{8,}" title="Password minimal 8 karakter">
                </div>

                <div class="form-group">
                    <label for="confirm-password">Konfirmasi Password</label>
                    <input type="password" id="confirm-password" name="new_password_confirmation">
                </div>
                <button type="submit" class="update-button">Update Profile</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const oldPassword = document.getElementById('old-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            // Kondisi ketika mengisi password baru tapi password lama kosong
            if (newPassword && !oldPassword) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Password lama harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Kondisi ketika password lama diisi tapi password baru kosong
            if (oldPassword && !newPassword) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Password baru harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Kondisi ketika password baru diisi tapi konfirmasi password kosong
            if (newPassword && !confirmPassword) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Konfirmasi password harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validasi panjang password minimal 8 karakter
            if (newPassword && newPassword.length < 8) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Password baru minimal 8 karakter!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validasi kecocokan password baru dengan konfirmasi password
            if (newPassword && newPassword !== confirmPassword) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Password baru dan konfirmasi password tidak cocok!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Jika semua validasi berhasil atau tidak ada perubahan password
            if (!oldPassword && !newPassword && !confirmPassword) {
                // Jika tidak ada perubahan password, langsung submit form
                this.submit();
            } else if (oldPassword && newPassword && confirmPassword && newPassword === confirmPassword) {
                // Jika ada perubahan password dan semua valid, submit form
                this.submit();
            }
        });
    </script>
</body>
</html>
