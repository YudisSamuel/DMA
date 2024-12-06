document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const typeFilter = document.getElementById('typeFilter');

    typeFilter.addEventListener('change', function() {
        const selectedType = this.value;
        const rows = document.querySelectorAll('#DataProductTableBody tr');

        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            if (selectedType === '' || rowType === selectedType) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    });

    // Select All Checkboxes functionality
    function toggleAllCheckboxes() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('#DataProductTableBody input[type="checkbox"]');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }

    // Initialize tooltips if needed
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
