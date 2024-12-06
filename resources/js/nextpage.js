document.addEventListener('DOMContentLoaded', function() {
    // Tangkap semua elemen yang dibutuhkan
    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    const typeFilter = document.getElementById('typeFilter');
    const nextPageBtn = document.getElementById('nextPage');
    const prevPageBtn = document.getElementById('prevPage');
    const firstPageBtn = document.getElementById('firstPage');
    const lastPageBtn = document.getElementById('lastPage');
    const currentPageSpan = document.getElementById('currentPage');
    const startEntrySpan = document.getElementById('startEntry');
    const endEntrySpan = document.getElementById('endEntry');
    const totalEntriesSpan = document.getElementById('totalEntries');

    // Fungsi untuk mengekstrak nomor halaman saat ini dan total halaman
    function getCurrentPageInfo() {
        const pageText = currentPageSpan.textContent;
        const [currentPage, totalPages] = pageText
            .replace('Page ', '')
            .split(' of ')
            .map(Number);
        return { currentPage, totalPages };
    }

    // Fungsi untuk memperbarui URL dengan parameter
    const updateURL = (params) => {
        const currentUrl = new URL(window.location.href);

        // Atur parameter
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== '') {
                currentUrl.searchParams.set(key, params[key]);
            } else {
                currentUrl.searchParams.delete(key);
            }
        });

        // Navigasi ke URL baru
        window.location.href = currentUrl.toString();
    };

    // Fungsi untuk mengupdate status tombol navigasi
    function updateNavigationButtons() {
        const { currentPage, totalPages } = getCurrentPageInfo();

        // Update status tombol
        prevPageBtn.disabled = currentPage <= 1;
        firstPageBtn.disabled = currentPage <= 1;
        nextPageBtn.disabled = currentPage >= totalPages;
        lastPageBtn.disabled = currentPage >= totalPages;
    }

    // Event listener untuk next page
    nextPageBtn.addEventListener('click', function() {
        const { currentPage, totalPages } = getCurrentPageInfo();

        if (currentPage < totalPages) {
            updateURL({
                page: currentPage + 1,
                perPage: rowsPerPageSelect.value,
                type: typeFilter.value || null
            });
        }
    });

    // Event listener untuk previous page
    prevPageBtn.addEventListener('click', function() {
        const { currentPage } = getCurrentPageInfo();

        if (currentPage > 1) {
            updateURL({
                page: currentPage - 1,
                perPage: rowsPerPageSelect.value,
                type: typeFilter.value || null
            });
        }
    });

    // Event listener untuk first page
    firstPageBtn.addEventListener('click', function() {
        updateURL({
            page: 1,
            perPage: rowsPerPageSelect.value,
            type: typeFilter.value || null
        });
    });

    // Event listener untuk last page
    lastPageBtn.addEventListener('click', function() {
        const { totalPages } = getCurrentPageInfo();

        updateURL({
            page: totalPages,
            perPage: rowsPerPageSelect.value,
            type: typeFilter.value || null
        });
    });

    // Event listener untuk filter rows per page
    rowsPerPageSelect.addEventListener('change', function() {
        updateURL({
            page: 1,
            perPage: this.value,
            type: typeFilter.value || null
        });
    });

    // Event listener untuk filter tipe produk
    typeFilter.addEventListener('change', function() {
        updateURL({
            page: 1,
            perPage: rowsPerPageSelect.value,
            type: this.value || null
        });
    });

    // Inisialisasi status tombol navigasi
    updateNavigationButtons();
});
