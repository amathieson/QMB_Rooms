import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import {VitePWA} from 'vite-plugin-pwa';
import { splitVendorChunkPlugin } from 'vite';

// https://vitejs.dev/config/
export default defineConfig({
  base: '/qmb/',
  plugins: [vue(),splitVendorChunkPlugin(),VitePWA({
    manifest: {
      "name": "QMB_Rooms",
      "short_name": "QMB_Rooms",
      "description": "The timetables and availability of all the Queen Mother Building's Rooms",
      "start_url": "/qmb/",
      "display": "standalone",
      "background_color": "#ffffff",
      "theme_color": "#646cff",
      "icons": [
        {
          "src": "/qmb/icons/icon-192.png",
          "sizes": "192x192",
          "type": "image/png"
        },
        {
          "src": "/qmb/icons/icon-192-maskable.png",
          "sizes": "192x192",
          "type": "image/png",
          "purpose": "maskable"
        },
        {
          "src": "/qmb/icons/icon-512.png",
          "sizes": "512x512",
          "type": "image/png"
        },
        {
          "src": "/qmb/icons/icon-512-maskable.png",
          "sizes": "512x512",
          "type": "image/png",
          "purpose": "maskable"
        },
      ],
      "screenshots":[
        {
          "src": "/qmb/screenshots/screen1_mobile.png",
          "sizes": "1442x2562",
          "type": "image/png",
          "form_factor": "narrow",
          "label": "View of the Labs Statuses"
        },
        {
          "src": "/qmb/screenshots/screen1_wide.png",
          "sizes": "2732x2048",
          "type": "image/png",
          "form_factor": "wide",
          "label": "View of the Labs Statuses"
        },
      ]
    },
    registerType: 'autoUpdate',
  })],
  build: {
    base: '/qmb/',
    minify: true,
    cssCodeSplit: true,
    modulePreload: false,
  }
})
