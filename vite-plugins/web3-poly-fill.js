/**
 * author @ofumbi <ofuzak@gmail.com>
 * Fixes issues with vite ^ 4.0
 * Moves all polyfills needed for web3 outside viet config
 */
const { NodeGlobalsPolyfillPlugin } = require("@esbuild-plugins/node-globals-polyfill");
const { NodeModulesPolyfillPlugin } = require("@esbuild-plugins/node-modules-polyfill");
const RollupPluginNodePolyfill = require("rollup-plugin-node-polyfills");
module.exports = function NgmiPolyfill(options = {}) {
    const {
        defineGlobal = true,
        nodeGlobalsOptions = {},
        nodeModulesOptions = {},
        rollupPolyfillOptions = {},
    } = options;
    return [
        {
            name: "web3-poly-fill",
            config: () => ({
                build: {
                    rollupOptions: {
                        plugins: [RollupPluginNodePolyfill(rollupPolyfillOptions)],
                    },
                },
                optimizeDeps: {
                    esbuildOptions: {
                        define: {
                            ...(defineGlobal ? { global: "globalThis" } : {}),
                        },
                        plugins: [
                            NodeGlobalsPolyfillPlugin({
                                buffer: true,
                                process: true,
                                ...nodeGlobalsOptions,
                            }),
                            NodeModulesPolyfillPlugin(nodeModulesOptions),
                            {
                                name: 'fix-node-globals-polyfill',
                                setup(build) {
                                    build.onResolve(
                                        { filter: /_virtual-process-polyfill_\.js/ },
                                        ({ path }) => ({ path })
                                    );
                                    build.onResolve(
                                        { filter: /_buffer\.js/ },
                                        ({ path }) => ({ path })
                                    );
                                }
                            }
                        ],
                    },
                },
            }),
        },
    ];
};
module.exports.default = module.exports;
