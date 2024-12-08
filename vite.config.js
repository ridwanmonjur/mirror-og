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
                'resources/js/libraries/lightpicker.js',
                'resources/js/libraries/tagify.js',
                'resources/js/libraries/file-edit.js',
                'resources/js/libraries/lightgallery.js',
                'resources/sass/libraries/lightgallery.scss',
                'resources/sass/libraries/file-edit.scss',
                'resources/sass/libraries/lightpicker.scss',
                'resources/js/libraries/colorpicker.js',
                'resources/sass/libraries/colorpicker.scss',
                'resources/sass/libraries/tagify.scss',
                'resources/js/alpine/chat.js',
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
                        if (
                            id.includes('@popperjs/core') || 
                            id.includes('bootstrap') || 
                            id.includes('sweetalert')
                        ) {
                            return 'vendor-core';
                        }

                        if (id.includes('firebase')) {
                            return 'vendor-firebase';
                        }

                        // All other vendor dependencies
                        return 'vendor-others';
                    }

                    // Handle your application code
                    if (id.includes('/resources/')) {
                        // Library groups
                        if (id.includes('/libraries/')) {
                            return 'lib-ui';
                        }

                        // Alpine components
                        if (id.includes('/alpine/')) {
                            return 'alpine-components';
                        }

                        // Styles
                        if (id.includes('.scss')) {
                            if (id.includes('/libraries/')) {
                                return 'lib-styles';
                            }
                            return 'styles';
                        }

                    }

                    return 'index';
                }
            }
        }
    },
    optimizeDeps: {
        include: ['bootstrap', '@popperjs/core', 'sweetalert2']
    }
});
