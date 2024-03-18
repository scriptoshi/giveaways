const fs = require('fs');
const path = require('path');
module.exports = function vpshotfile({ publicDirectory, origin } = {}) {
    return {
        name: 'vpshotfile',
        enforce: 'post',
        configureServer(server) {
            server.httpServer?.once('listening', () => {
                const address = server.httpServer?.address();
                const isAddressInfo = (x) => typeof x === 'object';
                const hotFile = path.join((publicDirectory ?? 'public'), 'hot');
                if (isAddressInfo(address) && origin) {
                    fs.writeFileSync(hotFile, origin);
                }
            });
        }
    };
};

module.exports.default = module.exports;
