function printRow(rowId) {
    // Mendapatkan elemen baris yang akan dicetak
    var row = document.getElementById(rowId);

    // Pastikan baris ditemukan
    if (!row) {
        console.error("Baris dengan ID " + rowId + " tidak ditemukan.");
        return;
    }

    // Membuka jendela baru untuk pencetakan
    var printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Row</title>');
    printWindow.document.write('<link rel="stylesheet" href="/css/laporan.css" type="text/css" />'); // Pastikan path CSS benar
    printWindow.document.write('</head><body>');

    // Menambahkan tabel dengan hanya satu baris
    printWindow.document.write('<table border="1" cellspacing="0" cellpadding="10"><thead><tr>' + row.innerHTML + '</tr></thead></table>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    // Lakukan pencetakan setelah konten dimuat
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close(); // Menutup jendela setelah pencetakan
    };
}


document.getElementById('clearButton').addEventListener('click', function (e) {
    e.preventDefault(); // Mencegah form submit dan berpindah halaman

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data di tabel akan dihapus.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Kirim permintaan POST ke server untuk memperbarui status data
            fetch('/laporan/clear', {
                method: 'POST', // Pastikan ini POST, bukan GET
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    // Data tambahan jika dibutuhkan (misalnya ID atau filter data)
                })
            })
            .then(response => response.json())
            .then(data => {
                // Menampilkan SweetAlert setelah data berhasil dihapus
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data di tabel telah dihapus.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                // Menghapus data dari tabel di halaman
                const tableBody = document.querySelector('#laporanTable tbody');
                if (tableBody) {
                    tableBody.innerHTML = ''; // Menghapus semua baris di tabel
                }
            })
            .catch(error => {
                // Menampilkan SweetAlert jika terjadi kesalahan
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat menghapus data.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
});





