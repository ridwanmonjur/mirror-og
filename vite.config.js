import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import purge from '@erbelion/vite-plugin-laravel-purgecss'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/lightgallery.js',
                'resources/sass/lightgallery.scss',
                'resources/js/file-upload-preview.js',
                'resources/sass/file-upload-preview.scss',
                'resources/js/colorpicker.js',
                'resources/sass/colorpicker.scss',
            ],
            refresh: true,
        }),
        purge({
            css: ['resources/sass/app-custom.scss'],
            templates: ['blade'],
            safelist: ['data-aos', 'ripple']
        })
    ],
    build: { minify: true },
});
