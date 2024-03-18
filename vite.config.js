import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import removeConsole from 'vite-plugin-remove-console';

import svgLoader from './vite-plugins/svgLoader.js';
import vpshotfile from './vite-plugins/vpshotfile.js';
const hash = Math.floor(Math.random() * 90000) + 10000;
export default defineConfig({
    /* server: {
        origin: "https://dev.makx.io:4000",
        port: 3000,
        hmr: {
            port:3000,
            clientPort: 4000,
        }
    }, */
    resolve: {
        alias: {
            'vue-i18n': 'vue-i18n/dist/vue-i18n.cjs.js',
        }
    },
    build: {
        sourcemap: false,
        output: {
            manualChunks: undefined
        },
        rollupOptions: {
            cache: false,
            maxParallelFileOps: 5,
            output: {
                manualChunks: undefined,
                entryFileNames: `[name]` + hash + `.js`,
                chunkFileNames: `[name]` + hash + `.js`,
                assetFileNames: `[name]` + hash + `.[ext]`
            },
        },
        commonjsOptions: {
            transformMixedEsModules: true,
        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        svgLoader(),
        removeConsole(),
        // web3PolyFill(),
        vpshotfile({
            // origin: "https://dev.makx.io:4000",
        }),
    ],
});
