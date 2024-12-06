
// resources/js/dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard JS loaded!');

    // Modal functionality
    initializeModal();

    // Dropdown functionality
    initializeDropdown();

    // Alert functionality
    initializeAlerts();
});

// resources/js/dashboard.js
document.addEventListener('DOMContentLoaded', function () {
    console.log('Inline JS loaded');
    const addButton = document.getElementById('addDataProduct');
    const modal = document.getElementById('DataProductModal');

    addButton.addEventListener('click', function () {
        modal.style.display = 'block';
    });

    const closeButton = modal.querySelector('.btn-danger');
    closeButton.addEventListener('click', function () {
        modal.style.display = 'none';
    });
});


function closeModal() {
    const modal = document.getElementById('DataProductModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function initializeDropdown() {
    const userProfile = document.querySelector('.user-profile');
    if (userProfile) {
        userProfile.addEventListener('click', function(event) {
            event.preventDefault();
            toggleDropdown();
        });
    }

    // Close dropdown when clicking outside
    window.addEventListener('click', function(event) {
        if (!event.target.matches('.user-profile') && !event.target.matches('.user-profile img')) {
            const dropdowns = document.getElementsByClassName("dropdown-content");
            Array.from(dropdowns).forEach(dropdown => {
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });
}

function toggleDropdown() {
    const dropdown = document.getElementById("userDropdown");
    if (dropdown) {
        dropdown.classList.toggle("show");
    }
}

function initializeAlerts() {
    // Handle close button on notifications
    document.querySelectorAll('.closebtn').forEach(button => {
        button.addEventListener('click', function() {
            const alertBox = this.parentElement;
            hideAlert(alertBox);
        });
    });

    // Auto-hide notifications after 5 seconds
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => hideAlert(alert), 5000);
    }
}

function hideAlert(alertElement) {
    alertElement.classList.add('hide');
    setTimeout(() => {
        alertElement.style.display = 'none';
    }, 500);
}


// Ambil data penjualan bulanan dari view yang dikirim oleh controller
const penjualanBulanan = JSON.parse(document.getElementById('penjualanBulanan').textContent);

// Inisialisasi Chart.js
const ctx = document.getElementById('earningsChart').getContext('2d');
const earningsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ],
        datasets: [{
            label: 'Penjualan (Unit)',
            data: penjualanBulanan,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Jumlah Terjual'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Bulan'
                }
            }
        }
    }
});


document.addEventListener('DOMContentLoaded', function () {
    const keuntunganBulanan = JSON.parse(document.getElementById('dataKeuntungan').textContent);

    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ],
            datasets: [{
                label: 'Keuntungan',
                data: keuntunganBulanan, // Data keuntungan per bulan
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Keuntungan (Rp)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                }
            }
        }
    });
});

document.getElementById("profileIcon").addEventListener("click", function() {
    var dropdown = document.getElementById("profileDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
});

window.addEventListener("click", function(event) {
    if (!event.target.matches('#profileIcon')) {
        var dropdown = document.getElementById("profileDropdown");
        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
        }
    }
});


function updateTotalPenjualan() {
    // Ambil nilai tahun dari dropdown
    const selectedYear = document.getElementById('yearDropdown').value;

    // Lakukan request AJAX ke server untuk mendapatkan data penjualan berdasarkan tahun
    fetch(`/getTotalPenjualan?year=${selectedYear}`)
      .then(response => response.json())
      .then(data => {
        // Update tampilan total penjualan dengan data yang diterima
        document.getElementById('totalPenjualan').textContent = `${data.totalPenjualan} Unit`;
      })
      .catch(error => console.error('Error fetching data:', error));
  }


  fetch(form.action, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: formData,
})
