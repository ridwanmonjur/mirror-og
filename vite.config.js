import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/alpine/settings.js',
                'resources/js/alpine/chat2.js',
            ],
            refresh: true,
        }),
        // pluginPurgeCss({
        //     content: [
        //         "**/*.js",
        //         "**/*.blade.php",
        //     ],
        //     css: ['resources/sass/bootstrap/app-cutom.scss'],
        //     variables: true,
        // }),
    ],
    build: {
        minify: true,
        rollupOptions: {
            // input: {
            //     app: 'resources/js/app.js',
            //     styles: 'resources/sass/app.scss',
                // chat: 'resources/js/alpine/chat2.js',
                // bracket: 'resources/js/alpine/bracket.js',
                // organizer: 'resources/js/alpine/organizer.js',
                // participant: 'resources/js/alpine/participant.js',
                // teamhead: 'resources/js/alpine/teamhead.js',
                // settings: 'resources/js/alpine/settings.js',
            // },
            output: {
                manualChunks(id) {
                    if (id.includes('intl-tel-input') || id.includes('sweetalert2') || id.includes('bootstrap') || id.includes('colorpicker')) {
                        return 'core-ui';
                    }
                    if (id.includes('firebase')) {
                        return 'firebase';
                    }
                  
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }

                },
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.endsWith('.woff2')) {
                        return 'assets/fonts/[name].[hash][extname]';
                    }
                    return 'assets/[name].[hash][extname]';
                },
            }
        }
    },
    optimizeDeps: {
        include: [
            'bootstrap',
            '@popperjs/core',
            'sweetalert2',
            'colorpicker',
            'firebase/app',
            'firebase/firestore',
            'firebase/auth'
        ]
    }
});
