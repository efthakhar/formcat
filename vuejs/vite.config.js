import {fileURLToPath, URL} from 'node:url'

import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'


export default defineConfig({

  //base: document.location.origin+document.location.pathname+'?page=formcat#',
  base: '/wp-content/plugins/formcat/vuejs/dist/',
  
  plugins: [
    vue(),
  ],

  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src',import.meta.url))
    }
  },

  build: {
    rollupOptions: {
      output: {
        assetFileNames: '[name].[ext]',
        chunkFileNames: '[name].js',
        entryFileNames: '[name].js'
      }
    },
  }

})