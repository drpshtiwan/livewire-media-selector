import { defineConfig } from 'vite'
import path from 'node:path'

// Build Tailwind CSS from resources/css/media-selector.css into dist/media-selector.css
export default defineConfig({
  root: process.cwd(),
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        'media-selector': path.resolve(__dirname, 'resources/css/media-selector.css')
      },
      output: {
        assetFileNames: (assetInfo) => {
          // Force a stable filename
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'media-selector.css'
          }
          return '[name][extname]'
        },
        entryFileNames: '[name].js'
      }
    }
  }
})


