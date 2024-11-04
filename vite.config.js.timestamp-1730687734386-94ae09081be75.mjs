// vite.config.js
import { defineConfig } from "file:///home/ridwan/Oceans/driftwood/node_modules/vite/dist/node/index.js";
import laravel from "file:///home/ridwan/Oceans/driftwood/node_modules/laravel-vite-plugin/dist/index.mjs";
import pluginPurgeCss from "file:///home/ridwan/Oceans/driftwood/node_modules/@mojojoejo/vite-plugin-purgecss/dist/index.mjs";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/sass/app.scss",
        "resources/js/app.js",
        "resources/js/libraries/tippy.js",
        "resources/js/libraries/tagify.js",
        "resources/js/libraries/alpine.js",
        "resources/js/libraries/file-edit.js",
        "resources/js/libraries/lightgallery.js",
        "resources/sass/libraries/lightgallery.scss",
        "resources/sass/libraries/file-edit.scss",
        "resources/js/libraries/file-upload-preview.js",
        "resources/sass/libraries/file-upload-preview.scss",
        "resources/js/libraries/colorpicker.js",
        "resources/sass/libraries/colorpicker.scss",
        "resources/sass/libraries/tagify.scss",
        "resources/js/pages/chat.js",
        "resources/js/pages/bracket.js"
      ],
      refresh: true
    }),
    pluginPurgeCss({
      content: [
        "**/*.js",
        "**/*.blade.php"
      ],
      css: ["resources/sass/bootstrap/app-cutom.scss"],
      variables: true
    })
  ],
  build: {
    minify: true,
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes("node_modules")) {
            return id.toString().split("node_modules/")[1].split("/")[0].toString();
          }
        }
      }
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvaG9tZS9yaWR3YW4vT2NlYW5zL2RyaWZ0d29vZFwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiL2hvbWUvcmlkd2FuL09jZWFucy9kcmlmdHdvb2Qvdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL2hvbWUvcmlkd2FuL09jZWFucy9kcmlmdHdvb2Qvdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJztcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xuaW1wb3J0IHBsdWdpblB1cmdlQ3NzIGZyb20gXCJAbW9qb2pvZWpvL3ZpdGUtcGx1Z2luLXB1cmdlY3NzXCI7XG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBbXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9zYXNzL2FwcC5zY3NzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2FwcC5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9saWJyYXJpZXMvdGlwcHkuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvbGlicmFyaWVzL3RhZ2lmeS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9saWJyYXJpZXMvYWxwaW5lLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2xpYnJhcmllcy9maWxlLWVkaXQuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvbGlicmFyaWVzL2xpZ2h0Z2FsbGVyeS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9zYXNzL2xpYnJhcmllcy9saWdodGdhbGxlcnkuc2NzcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9zYXNzL2xpYnJhcmllcy9maWxlLWVkaXQuc2NzcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9saWJyYXJpZXMvZmlsZS11cGxvYWQtcHJldmlldy5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9zYXNzL2xpYnJhcmllcy9maWxlLXVwbG9hZC1wcmV2aWV3LnNjc3MnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvbGlicmFyaWVzL2NvbG9ycGlja2VyLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL3Nhc3MvbGlicmFyaWVzL2NvbG9ycGlja2VyLnNjc3MnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvc2Fzcy9saWJyYXJpZXMvdGFnaWZ5LnNjc3MnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcGFnZXMvY2hhdC5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9wYWdlcy9icmFja2V0LmpzJyxcbiAgICAgICAgICAgIF0sXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICAgICAgcGx1Z2luUHVyZ2VDc3Moe1xuICAgICAgICAgICAgY29udGVudDogW1xuICAgICAgICAgICAgICAgIFwiKiovKi5qc1wiLFxuICAgICAgICAgICAgICAgIFwiKiovKi5ibGFkZS5waHBcIixcbiAgICAgICAgICAgIF0sXG4gICAgICAgICAgICBjc3M6IFsncmVzb3VyY2VzL3Nhc3MvYm9vdHN0cmFwL2FwcC1jdXRvbS5zY3NzJ10sXG4gICAgICAgICAgICB2YXJpYWJsZXM6IHRydWUsXG4gICAgICAgIH0pLFxuICAgIF0sXG4gICAgYnVpbGQ6IHsgXG4gICAgICAgIG1pbmlmeTogdHJ1ZSwgXG4gICAgICAgIHJvbGx1cE9wdGlvbnM6IHtcbiAgICAgICAgICAgIG91dHB1dDp7XG4gICAgICAgICAgICAgICAgbWFudWFsQ2h1bmtzKGlkKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChpZC5pbmNsdWRlcygnbm9kZV9tb2R1bGVzJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBpZC50b1N0cmluZygpLnNwbGl0KCdub2RlX21vZHVsZXMvJylbMV0uc3BsaXQoJy8nKVswXS50b1N0cmluZygpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUF5USxTQUFTLG9CQUFvQjtBQUN0UyxPQUFPLGFBQWE7QUFDcEIsT0FBTyxvQkFBb0I7QUFFM0IsSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDeEIsU0FBUztBQUFBLElBQ0wsUUFBUTtBQUFBLE1BQ0osT0FBTztBQUFBLFFBQ0g7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxNQUNKO0FBQUEsTUFDQSxTQUFTO0FBQUEsSUFDYixDQUFDO0FBQUEsSUFDRCxlQUFlO0FBQUEsTUFDWCxTQUFTO0FBQUEsUUFDTDtBQUFBLFFBQ0E7QUFBQSxNQUNKO0FBQUEsTUFDQSxLQUFLLENBQUMseUNBQXlDO0FBQUEsTUFDL0MsV0FBVztBQUFBLElBQ2YsQ0FBQztBQUFBLEVBQ0w7QUFBQSxFQUNBLE9BQU87QUFBQSxJQUNILFFBQVE7QUFBQSxJQUNSLGVBQWU7QUFBQSxNQUNYLFFBQU87QUFBQSxRQUNILGFBQWEsSUFBSTtBQUNiLGNBQUksR0FBRyxTQUFTLGNBQWMsR0FBRztBQUM3QixtQkFBTyxHQUFHLFNBQVMsRUFBRSxNQUFNLGVBQWUsRUFBRSxDQUFDLEVBQUUsTUFBTSxHQUFHLEVBQUUsQ0FBQyxFQUFFLFNBQVM7QUFBQSxVQUMxRTtBQUFBLFFBQ0o7QUFBQSxNQUNKO0FBQUEsSUFDSjtBQUFBLEVBQ0o7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
