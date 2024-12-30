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
                'resources/js/libraries/petite-vue.js',
                'resources/js/libraries/lightpicker.js',
                'resources/js/libraries/tagify.js',
                'resources/js/libraries/file-edit.js',
                'resources/js/libraries/lightgallery.js',
                'resources/js/libraries/motion.js',
                'resources/sass/libraries/lightgallery.scss',
                'resources/sass/libraries/file-edit.scss',
                'resources/sass/libraries/lightpicker.scss',
                'resources/js/libraries/colorpicker.js',
                'resources/sass/libraries/colorpicker.scss',
                'resources/sass/libraries/tagify.scss',
                'resources/js/alpine/chat2.js',
                'resources/js/alpine/bracket.js',
                'resources/js/alpine/organizer.js',
                'resources/js/alpine/participant.js',
                'resources/js/alpine/teamhead.js',
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
            output: {
                manualChunks(id) {
                    // Handle node_modules separately
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    }
                }
            }
        }
    },
    optimizeDeps: {
        include: ['bootstrap', '@popperjs/core', 'sweetalert2']
    }
});
