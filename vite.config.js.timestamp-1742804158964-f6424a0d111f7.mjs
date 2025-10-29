// vite.config.js
import { defineConfig, splitVendorChunkPlugin } from "file:///C:/laragon/www/grozaar/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/laragon/www/grozaar/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/laragon/www/grozaar/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import i18n from "file:///C:/laragon/www/grozaar/node_modules/laravel-vue-i18n/dist/vite.mjs";
import path from "path";
import legacy from "file:///C:/laragon/www/grozaar/node_modules/@vitejs/plugin-legacy/dist/index.mjs";
var vite_config_default = defineConfig({
  plugins: [
    splitVendorChunkPlugin(),
    laravel({
      input: ["resources/js/app.js"],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    i18n(),
    legacy({
      targets: ["defaults", "not IE 11"]
    })
  ],
  resolve: {
    alias: {
      "@": "/resources/js",
      // vue: "@vitejs/plugin-vue",
      vue: "vue/dist/vue.esm-bundler.js",
      ziggy: path.resolve("vendor/tightenco/ziggy/dist/index.esm.js"),
      jquery: "jquery"
    }
  },
  build: {
    chunkSizeWarningLimit: 1600
  }
  // define: {
  //     $: 'jquery',
  //     jQuery: 'jquery',
  // },
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxncm96YWFyXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxncm96YWFyXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi9sYXJhZ29uL3d3dy9ncm96YWFyL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHtkZWZpbmVDb25maWcsIHNwbGl0VmVuZG9yQ2h1bmtQbHVnaW59IGZyb20gJ3ZpdGUnO1xuaW1wb3J0IGxhcmF2ZWwgZnJvbSAnbGFyYXZlbC12aXRlLXBsdWdpbic7XG5pbXBvcnQgdnVlIGZyb20gXCJAdml0ZWpzL3BsdWdpbi12dWVcIjtcbmltcG9ydCBpMThuIGZyb20gXCJsYXJhdmVsLXZ1ZS1pMThuL3ZpdGVcIjtcbmltcG9ydCBwYXRoIGZyb20gJ3BhdGgnXG5pbXBvcnQgbGVnYWN5IGZyb20gJ0B2aXRlanMvcGx1Z2luLWxlZ2FjeSdcbi8vIGltcG9ydCBpbmplY3QgZnJvbSAnQHJvbGx1cC9wbHVnaW4taW5qZWN0Jztcbi8vIGltcG9ydCBDb21wb25lbnRzIGZyb20gXCJ1bnBsdWdpbi12dWUtY29tcG9uZW50cy92aXRlXCI7XG4vLyBpbXBvcnQgeyBGaWxlU3lzdGVtU2Nhbm5lciB9IGZyb20gXCJ1bnBsdWdpbi12dWUtY29tcG9uZW50cy9yZXNvbHZlcnNcIjtcblxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgICBwbHVnaW5zOiBbXG4gICAgICAgIHNwbGl0VmVuZG9yQ2h1bmtQbHVnaW4oKSxcbiAgICAgICAgbGFyYXZlbCh7XG4gICAgICAgICAgICBpbnB1dDogW1wicmVzb3VyY2VzL2pzL2FwcC5qc1wiXSxcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXG4gICAgICAgIH0pLFxuICAgICAgICB2dWUoe1xuICAgICAgICAgICAgdGVtcGxhdGU6IHtcbiAgICAgICAgICAgICAgICB0cmFuc2Zvcm1Bc3NldFVybHM6IHtcbiAgICAgICAgICAgICAgICAgICAgYmFzZTogbnVsbCxcbiAgICAgICAgICAgICAgICAgICAgaW5jbHVkZUFic29sdXRlOiBmYWxzZSxcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfSxcbiAgICAgICAgfSksXG4gICAgICAgIGkxOG4oKSxcbiAgICAgICAgbGVnYWN5KHtcbiAgICAgICAgICAgIHRhcmdldHM6IFsnZGVmYXVsdHMnLCAnbm90IElFIDExJ10sXG4gICAgICAgICAgfSksXG4gICAgXSxcbiAgICByZXNvbHZlOiB7XG4gICAgICAgIGFsaWFzOiB7XG4gICAgICAgICAgICBcIkBcIjogXCIvcmVzb3VyY2VzL2pzXCIsXG4gICAgICAgICAgICAvLyB2dWU6IFwiQHZpdGVqcy9wbHVnaW4tdnVlXCIsXG4gICAgICAgICAgICB2dWU6IFwidnVlL2Rpc3QvdnVlLmVzbS1idW5kbGVyLmpzXCIsXG4gICAgICAgICAgICB6aWdneTogcGF0aC5yZXNvbHZlKFwidmVuZG9yL3RpZ2h0ZW5jby96aWdneS9kaXN0L2luZGV4LmVzbS5qc1wiKSxcbiAgICAgICAgICAgIGpxdWVyeTogJ2pxdWVyeSdcbiAgICAgICAgfSxcbiAgICB9LFxuICAgIGJ1aWxkOiB7XG4gICAgICAgIGNodW5rU2l6ZVdhcm5pbmdMaW1pdDogMTYwMCxcbiAgICB9LFxuICAgIC8vIGRlZmluZToge1xuICAgIC8vICAgICAkOiAnanF1ZXJ5JyxcbiAgICAvLyAgICAgalF1ZXJ5OiAnanF1ZXJ5JyxcbiAgICAvLyB9LFxufSk7XG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQTRQLFNBQVEsY0FBYyw4QkFBNkI7QUFDL1MsT0FBTyxhQUFhO0FBQ3BCLE9BQU8sU0FBUztBQUNoQixPQUFPLFVBQVU7QUFDakIsT0FBTyxVQUFVO0FBQ2pCLE9BQU8sWUFBWTtBQUtuQixJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUN4QixTQUFTO0FBQUEsSUFDTCx1QkFBdUI7QUFBQSxJQUN2QixRQUFRO0FBQUEsTUFDSixPQUFPLENBQUMscUJBQXFCO0FBQUEsTUFDN0IsU0FBUztBQUFBLElBQ2IsQ0FBQztBQUFBLElBQ0QsSUFBSTtBQUFBLE1BQ0EsVUFBVTtBQUFBLFFBQ04sb0JBQW9CO0FBQUEsVUFDaEIsTUFBTTtBQUFBLFVBQ04saUJBQWlCO0FBQUEsUUFDckI7QUFBQSxNQUNKO0FBQUEsSUFDSixDQUFDO0FBQUEsSUFDRCxLQUFLO0FBQUEsSUFDTCxPQUFPO0FBQUEsTUFDSCxTQUFTLENBQUMsWUFBWSxXQUFXO0FBQUEsSUFDbkMsQ0FBQztBQUFBLEVBQ1A7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLE9BQU87QUFBQSxNQUNILEtBQUs7QUFBQTtBQUFBLE1BRUwsS0FBSztBQUFBLE1BQ0wsT0FBTyxLQUFLLFFBQVEsMENBQTBDO0FBQUEsTUFDOUQsUUFBUTtBQUFBLElBQ1o7QUFBQSxFQUNKO0FBQUEsRUFDQSxPQUFPO0FBQUEsSUFDSCx1QkFBdUI7QUFBQSxFQUMzQjtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBS0osQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
