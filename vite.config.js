import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/map-selector.js',
                'resources/css/leaflet-custom.css'
            ],
            refresh: true,
        }),
    ],
});
