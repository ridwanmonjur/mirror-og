import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import pluginPurgeCss from "@mojojoejo/vite-plugin-purgecss";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/libraries/tippy.js',
                'resources/js/libraries/tagify.js',
                'resources/js/libraries/alpine.js',
                'resources/js/libraries/file-edit.js',
                'resources/js/libraries/lightgallery.js',
                'resources/sass/libraries/lightgallery.scss',
                'resources/sass/libraries/file-edit.scss',
                'resources/js/libraries/file-upload-preview.js',
                'resources/sass/libraries/file-upload-preview.scss',
                'resources/js/libraries/colorpicker.js',
                'resources/sass/libraries/colorpicker.scss',
                'resources/sass/libraries/tagify.scss',
                'resources/js/pages/chat.js',
                'resources/js/pages/bracket.js',
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
