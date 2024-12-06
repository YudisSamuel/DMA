import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/dashboard.js','resources/js/navbar.js','resources/js/dma-prediction.js','resources/js/product-filter.js','resources/js/ceklist-kolom.js','resources/js/laporan.js','resources/js/nextpage.js','resources/js/user.js'],
            refresh: true,
        }),
    ],
});
