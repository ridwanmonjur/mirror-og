// vite.config.js
import { defineConfig } from "file:///home/ridwan/Oceans/driftwood_fork/node_modules/vite/dist/node/index.js";
import laravel from "file:///home/ridwan/Oceans/driftwood_fork/node_modules/laravel-vite-plugin/dist/index.mjs";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/sass/betaapp.scss",
        "resources/js/betaapp.js",
        "resources/sass/app.scss",
        "resources/js/app.js",
        "resources/js/alpine/settings.js",
        "resources/js/alpine/chat.js",
        "resources/js/alpine/teamhead.js",
        "resources/js/alpine/organizer.js",
        "resources/js/alpine/adminBrackets.js",
        "resources/js/alpine/participant.js",
        "resources/js/alpine/notifications.js",
        "resources/js/custom/share.js"
      ],
      refresh: true
    })
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
      // chat: 'resources/js/alpine/chat.js',
      // bracket: 'resources/js/alpine/bracket.js',
      // organizer: 'resources/js/alpine/organizer.js',
      // participant: 'resources/js/alpine/participant.js',
      // teamhead: 'resources/js/alpine/teamhead.js',
      // settings: 'resources/js/alpine/settings.js',
      // },
      output: {
        manualChunks(id) {
          if (id.includes("intl-tel-input") || id.includes("stripe") || id.includes("colorpicker")) {
            return "extra-ui";
          }
          if (id.includes("sweetalert2") || id.includes("bootstrap")) {
            return "core-ui";
          }
          if (id.includes("firebase")) {
            return "firebase";
          }
          if (id.includes("node_modules")) {
            return "vendor";
          }
        },
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith(".woff2")) {
            return "assets/fonts/[name].[hash][extname]";
          }
          return "assets/[name].[hash][extname]";
        }
      }
    }
  },
  optimizeDeps: {
    include: [
      "bootstrap",
      "@popperjs/core",
      "sweetalert2",
      "colorpicker",
      "firebase/app",
      "firebase/firestore",
      "firebase/auth"
    ]
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvaG9tZS9yaWR3YW4vT2NlYW5zL2RyaWZ0d29vZF9mb3JrXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCIvaG9tZS9yaWR3YW4vT2NlYW5zL2RyaWZ0d29vZF9mb3JrL3ZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9ob21lL3JpZHdhbi9PY2VhbnMvZHJpZnR3b29kX2Zvcmsvdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJztcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgIHBsdWdpbnM6IFtcbiAgICAgICAgbGFyYXZlbCh7XG4gICAgICAgICAgICBpbnB1dDogW1xuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvc2Fzcy9iZXRhYXBwLnNjc3MnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvYmV0YWFwcC5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9zYXNzL2FwcC5zY3NzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2FwcC5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9hbHBpbmUvc2V0dGluZ3MuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvYWxwaW5lL2NoYXQuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvYWxwaW5lL3RlYW1oZWFkLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2FscGluZS9vcmdhbml6ZXIuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvYWxwaW5lL2FkbWluQnJhY2tldHMuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvYWxwaW5lL3BhcnRpY2lwYW50LmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2FscGluZS9ub3RpZmljYXRpb25zLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2N1c3RvbS9zaGFyZS5qcydcbiAgICAgICAgICAgIF0sXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICAgICAgLy8gcGx1Z2luUHVyZ2VDc3Moe1xuICAgICAgICAvLyAgICAgY29udGVudDogW1xuICAgICAgICAvLyAgICAgICAgIFwiKiovKi5qc1wiLFxuICAgICAgICAvLyAgICAgICAgIFwiKiovKi5ibGFkZS5waHBcIixcbiAgICAgICAgLy8gICAgIF0sXG4gICAgICAgIC8vICAgICBjc3M6IFsncmVzb3VyY2VzL3Nhc3MvYm9vdHN0cmFwL2FwcC1jdXRvbS5zY3NzJ10sXG4gICAgICAgIC8vICAgICB2YXJpYWJsZXM6IHRydWUsXG4gICAgICAgIC8vIH0pLFxuICAgIF0sXG4gICAgYnVpbGQ6IHtcbiAgICAgICAgbWluaWZ5OiB0cnVlLFxuICAgICAgICByb2xsdXBPcHRpb25zOiB7XG4gICAgICAgICAgICAvLyBpbnB1dDoge1xuICAgICAgICAgICAgLy8gICAgIGFwcDogJ3Jlc291cmNlcy9qcy9hcHAuanMnLFxuICAgICAgICAgICAgLy8gICAgIHN0eWxlczogJ3Jlc291cmNlcy9zYXNzL2FwcC5zY3NzJyxcbiAgICAgICAgICAgICAgICAvLyBjaGF0OiAncmVzb3VyY2VzL2pzL2FscGluZS9jaGF0LmpzJyxcbiAgICAgICAgICAgICAgICAvLyBicmFja2V0OiAncmVzb3VyY2VzL2pzL2FscGluZS9icmFja2V0LmpzJyxcbiAgICAgICAgICAgICAgICAvLyBvcmdhbml6ZXI6ICdyZXNvdXJjZXMvanMvYWxwaW5lL29yZ2FuaXplci5qcycsXG4gICAgICAgICAgICAgICAgLy8gcGFydGljaXBhbnQ6ICdyZXNvdXJjZXMvanMvYWxwaW5lL3BhcnRpY2lwYW50LmpzJyxcbiAgICAgICAgICAgICAgICAvLyB0ZWFtaGVhZDogJ3Jlc291cmNlcy9qcy9hbHBpbmUvdGVhbWhlYWQuanMnLFxuICAgICAgICAgICAgICAgIC8vIHNldHRpbmdzOiAncmVzb3VyY2VzL2pzL2FscGluZS9zZXR0aW5ncy5qcycsXG4gICAgICAgICAgICAvLyB9LFxuICAgICAgICAgICAgb3V0cHV0OiB7XG4gICAgICAgICAgICAgICAgbWFudWFsQ2h1bmtzKGlkKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChpZC5pbmNsdWRlcygnaW50bC10ZWwtaW5wdXQnKSB8fCBpZC5pbmNsdWRlcygnc3RyaXBlJykgfHwgaWQuaW5jbHVkZXMoJ2NvbG9ycGlja2VyJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiAnZXh0cmEtdWknO1xuICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGlkLmluY2x1ZGVzKCdzd2VldGFsZXJ0MicpIHx8IGlkLmluY2x1ZGVzKCdib290c3RyYXAnKSApIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiAnY29yZS11aSc7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgaWYgKGlkLmluY2x1ZGVzKCdmaXJlYmFzZScpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gJ2ZpcmViYXNlJztcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgICAgIGlmIChpZC5pbmNsdWRlcygnbm9kZV9tb2R1bGVzJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiAndmVuZG9yJztcbiAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICBhc3NldEZpbGVOYW1lczogKGFzc2V0SW5mbykgPT4ge1xuICAgICAgICAgICAgICAgICAgICBpZiAoYXNzZXRJbmZvLm5hbWUuZW5kc1dpdGgoJy53b2ZmMicpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gJ2Fzc2V0cy9mb250cy9bbmFtZV0uW2hhc2hdW2V4dG5hbWVdJztcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gJ2Fzc2V0cy9bbmFtZV0uW2hhc2hdW2V4dG5hbWVdJztcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfSxcbiAgICBvcHRpbWl6ZURlcHM6IHtcbiAgICAgICAgaW5jbHVkZTogW1xuICAgICAgICAgICAgJ2Jvb3RzdHJhcCcsXG4gICAgICAgICAgICAnQHBvcHBlcmpzL2NvcmUnLFxuICAgICAgICAgICAgJ3N3ZWV0YWxlcnQyJyxcbiAgICAgICAgICAgICdjb2xvcnBpY2tlcicsXG4gICAgICAgICAgICAnZmlyZWJhc2UvYXBwJyxcbiAgICAgICAgICAgICdmaXJlYmFzZS9maXJlc3RvcmUnLFxuICAgICAgICAgICAgJ2ZpcmViYXNlL2F1dGgnXG4gICAgICAgIF1cbiAgICB9XG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBd1IsU0FBUyxvQkFBb0I7QUFDclQsT0FBTyxhQUFhO0FBRXBCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxRQUNIO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxNQUNKO0FBQUEsTUFDQSxTQUFTO0FBQUEsSUFDYixDQUFDO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBLEVBU0w7QUFBQSxFQUNBLE9BQU87QUFBQSxJQUNILFFBQVE7QUFBQSxJQUNSLGVBQWU7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBLE1BV1gsUUFBUTtBQUFBLFFBQ0osYUFBYSxJQUFJO0FBQ2IsY0FBSSxHQUFHLFNBQVMsZ0JBQWdCLEtBQUssR0FBRyxTQUFTLFFBQVEsS0FBSyxHQUFHLFNBQVMsYUFBYSxHQUFHO0FBQ3RGLG1CQUFPO0FBQUEsVUFDWDtBQUVBLGNBQUksR0FBRyxTQUFTLGFBQWEsS0FBSyxHQUFHLFNBQVMsV0FBVyxHQUFJO0FBQ3pELG1CQUFPO0FBQUEsVUFDWDtBQUNBLGNBQUksR0FBRyxTQUFTLFVBQVUsR0FBRztBQUN6QixtQkFBTztBQUFBLFVBQ1g7QUFFQSxjQUFJLEdBQUcsU0FBUyxjQUFjLEdBQUc7QUFDN0IsbUJBQU87QUFBQSxVQUNYO0FBQUEsUUFFSjtBQUFBLFFBQ0EsZ0JBQWdCLENBQUMsY0FBYztBQUMzQixjQUFJLFVBQVUsS0FBSyxTQUFTLFFBQVEsR0FBRztBQUNuQyxtQkFBTztBQUFBLFVBQ1g7QUFDQSxpQkFBTztBQUFBLFFBQ1g7QUFBQSxNQUNKO0FBQUEsSUFDSjtBQUFBLEVBQ0o7QUFBQSxFQUNBLGNBQWM7QUFBQSxJQUNWLFNBQVM7QUFBQSxNQUNMO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsTUFDQTtBQUFBLE1BQ0E7QUFBQSxNQUNBO0FBQUEsSUFDSjtBQUFBLEVBQ0o7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
