import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  base: '/site/',
  plugins: [react()],
  server: {
    port: 5173,
    open: false,
    host: true,
  },
  build: {
    outDir: '../../public/site',
    emptyOutDir: true,
    manifest: true,
    target: 'esnext',
    cssCodeSplit: true,
    chunkSizeWarningLimit: 800,
    rollupOptions: {
      output: {
        manualChunks: {
          'vendor-three': ['three'],
          'vendor-motion': ['framer-motion'],
          'vendor-gsap': ['gsap'],
          'vendor-icons': ['lucide-react'],
        },
      },
    },
  },
});
