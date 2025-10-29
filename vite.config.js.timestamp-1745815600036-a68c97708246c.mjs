// vite.config.js
import { defineConfig, splitVendorChunkPlugin } from "file:///mnt/d/environment/laragon/www/grozaar/node_modules/vite/dist/node/index.js";
import laravel from "file:///mnt/d/environment/laragon/www/grozaar/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///mnt/d/environment/laragon/www/grozaar/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import i18n from "file:///mnt/d/environment/laragon/www/grozaar/node_modules/laravel-vue-i18n/dist/vite.mjs";
import path from "path";
import legacy from "file:///mnt/d/environment/laragon/www/grozaar/node_modules/@vitejs/plugin-legacy/dist/index.mjs";
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvbW50L2QvZW52aXJvbm1lbnQvbGFyYWdvbi93d3cvZ3JvemFhclwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiL21udC9kL2Vudmlyb25tZW50L2xhcmFnb24vd3d3L2dyb3phYXIvdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL21udC9kL2Vudmlyb25tZW50L2xhcmFnb24vd3d3L2dyb3phYXIvdml0ZS5jb25maWcuanNcIjtpbXBvcnQge2RlZmluZUNvbmZpZywgc3BsaXRWZW5kb3JDaHVua1BsdWdpbn0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCB2dWUgZnJvbSBcIkB2aXRlanMvcGx1Z2luLXZ1ZVwiO1xuaW1wb3J0IGkxOG4gZnJvbSBcImxhcmF2ZWwtdnVlLWkxOG4vdml0ZVwiO1xuaW1wb3J0IHBhdGggZnJvbSAncGF0aCdcbmltcG9ydCBsZWdhY3kgZnJvbSAnQHZpdGVqcy9wbHVnaW4tbGVnYWN5J1xuLy8gaW1wb3J0IGluamVjdCBmcm9tICdAcm9sbHVwL3BsdWdpbi1pbmplY3QnO1xuLy8gaW1wb3J0IENvbXBvbmVudHMgZnJvbSBcInVucGx1Z2luLXZ1ZS1jb21wb25lbnRzL3ZpdGVcIjtcbi8vIGltcG9ydCB7IEZpbGVTeXN0ZW1TY2FubmVyIH0gZnJvbSBcInVucGx1Z2luLXZ1ZS1jb21wb25lbnRzL3Jlc29sdmVyc1wiO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgIHBsdWdpbnM6IFtcbiAgICAgICAgc3BsaXRWZW5kb3JDaHVua1BsdWdpbigpLFxuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBbXCJyZXNvdXJjZXMvanMvYXBwLmpzXCJdLFxuICAgICAgICAgICAgcmVmcmVzaDogdHJ1ZSxcbiAgICAgICAgfSksXG4gICAgICAgIHZ1ZSh7XG4gICAgICAgICAgICB0ZW1wbGF0ZToge1xuICAgICAgICAgICAgICAgIHRyYW5zZm9ybUFzc2V0VXJsczoge1xuICAgICAgICAgICAgICAgICAgICBiYXNlOiBudWxsLFxuICAgICAgICAgICAgICAgICAgICBpbmNsdWRlQWJzb2x1dGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9LFxuICAgICAgICB9KSxcbiAgICAgICAgaTE4bigpLFxuICAgICAgICBsZWdhY3koe1xuICAgICAgICAgICAgdGFyZ2V0czogWydkZWZhdWx0cycsICdub3QgSUUgMTEnXSxcbiAgICAgICAgICB9KSxcbiAgICBdLFxuICAgIHJlc29sdmU6IHtcbiAgICAgICAgYWxpYXM6IHtcbiAgICAgICAgICAgIFwiQFwiOiBcIi9yZXNvdXJjZXMvanNcIixcbiAgICAgICAgICAgIC8vIHZ1ZTogXCJAdml0ZWpzL3BsdWdpbi12dWVcIixcbiAgICAgICAgICAgIHZ1ZTogXCJ2dWUvZGlzdC92dWUuZXNtLWJ1bmRsZXIuanNcIixcbiAgICAgICAgICAgIHppZ2d5OiBwYXRoLnJlc29sdmUoXCJ2ZW5kb3IvdGlnaHRlbmNvL3ppZ2d5L2Rpc3QvaW5kZXguZXNtLmpzXCIpLFxuICAgICAgICAgICAganF1ZXJ5OiAnanF1ZXJ5J1xuICAgICAgICB9LFxuICAgIH0sXG4gICAgYnVpbGQ6IHtcbiAgICAgICAgY2h1bmtTaXplV2FybmluZ0xpbWl0OiAxNjAwLFxuICAgIH0sXG4gICAgLy8gZGVmaW5lOiB7XG4gICAgLy8gICAgICQ6ICdqcXVlcnknLFxuICAgIC8vICAgICBqUXVlcnk6ICdqcXVlcnknLFxuICAgIC8vIH0sXG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBb1MsU0FBUSxjQUFjLDhCQUE2QjtBQUN2VixPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBQ2hCLE9BQU8sVUFBVTtBQUNqQixPQUFPLFVBQVU7QUFDakIsT0FBTyxZQUFZO0FBS25CLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLHVCQUF1QjtBQUFBLElBQ3ZCLFFBQVE7QUFBQSxNQUNKLE9BQU8sQ0FBQyxxQkFBcUI7QUFBQSxNQUM3QixTQUFTO0FBQUEsSUFDYixDQUFDO0FBQUEsSUFDRCxJQUFJO0FBQUEsTUFDQSxVQUFVO0FBQUEsUUFDTixvQkFBb0I7QUFBQSxVQUNoQixNQUFNO0FBQUEsVUFDTixpQkFBaUI7QUFBQSxRQUNyQjtBQUFBLE1BQ0o7QUFBQSxJQUNKLENBQUM7QUFBQSxJQUNELEtBQUs7QUFBQSxJQUNMLE9BQU87QUFBQSxNQUNILFNBQVMsQ0FBQyxZQUFZLFdBQVc7QUFBQSxJQUNuQyxDQUFDO0FBQUEsRUFDUDtBQUFBLEVBQ0EsU0FBUztBQUFBLElBQ0wsT0FBTztBQUFBLE1BQ0gsS0FBSztBQUFBO0FBQUEsTUFFTCxLQUFLO0FBQUEsTUFDTCxPQUFPLEtBQUssUUFBUSwwQ0FBMEM7QUFBQSxNQUM5RCxRQUFRO0FBQUEsSUFDWjtBQUFBLEVBQ0o7QUFBQSxFQUNBLE9BQU87QUFBQSxJQUNILHVCQUF1QjtBQUFBLEVBQzNCO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFLSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
