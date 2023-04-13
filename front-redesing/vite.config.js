import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import { resolve } from "path";
import { viteStaticCopy } from 'vite-plugin-static-copy'
// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue(),
    viteStaticCopy({
      targets: [
        {
          src: 'src/assets/*',
          dest: 'assets'
        },
        // {
        //   src: 'src/assets/css',
        //   dest: 'assets'
        // },
        // {
        //   src: 'src/assets/fonts',
        //   dest: 'assets'
        // },
        // {
        //   src: 'src/assets/images',
        //   dest: 'assets'
        // }
      ]
    })
  ],
  base: "/",
  resolve: {
    alias: {
      "@": resolve(__dirname, "src")
    }
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@use "@/style/_variables.scss" as *;`
      }
    }
  },
  server: {
    host: "0.0.0.0"
  }
});
