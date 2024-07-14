import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import pluginPurgeCss from "@mojojoejo/vite-plugin-purgecss";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/alpine.js',
                'resources/js/file-edit.js',
                'resources/js/lightgallery.js',
                'resources/sass/lightgallery.scss',
                'resources/sass/file-edit.scss',
                'resources/js/file-upload-preview.js',
                'resources/sass/file-upload-preview.scss',
                'resources/js/colorpicker.js',
                'resources/sass/colorpicker.scss',
                'resources/js/chat.js',
                'resources/sass/colorpicker.scss',
            ],
            refresh: true,
        }),
        pluginPurgeCss({
            content: [
                "**/*.js",
                "**/*.blade.php",
            ],
            css: ['resources/sass/bootstrap/app-cutom.scss'],
            variables: true,
        }),
    ],
    build: { 
        minify: true, 
        rollupOptions: {
            output:{
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    }
                }
            }
        }
    },
});
