document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("selectAll");
    const rowCheckboxes = document.querySelectorAll(".rowCheckbox");
    const deleteButton = document.querySelector("#deleteSelectedButton");

    // Fungsi untuk memilih atau membatalkan semua checkbox
    function toggleAllCheckboxes() {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked; // Set checkbox baris sesuai status "Select All"
        });
    }

    // Tambahkan event listener untuk checkbox "Select All"
    selectAllCheckbox.addEventListener("change", toggleAllCheckboxes);

    // Fungsi untuk menghapus baris yang dipilih
    function deleteSelectedRows() {
        const checkedBoxes = document.querySelectorAll(".rowCheckbox:checked");

        if (checkedBoxes.length === 0) {
            alert("Pilih data yang ingin dihapus.");
            return;
        }

        // Konfirmasi sebelum menghapus
        if (confirm("Apakah Anda yakin ingin menghapus data yang dipilih?")) {
            checkedBoxes.forEach(checkbox => {
                const row = checkbox.closest("tr");
                row.remove();
            });
        }
    }

    // Event listener pada tombol delete
    if (deleteButton) {
        deleteButton.addEventListener("click", deleteSelectedRows);
    }
});
