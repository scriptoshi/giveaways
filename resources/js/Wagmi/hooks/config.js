import { createConfig } from "use-wagmi";
import { coinbaseWallet, walletConnect } from 'use-wagmi/connectors';
import { fallback, http, webSocket } from "viem";
import * as SupportedChains from "viem/chains";


export const useWagmiConfig = (chains = [], walletConnectConfig) => {
    // Set up wagmi config
    const chainList = chains.map(c => c.chainId.toString());
    const viemChains = Object.values(SupportedChains).filter(s => chainList.includes(s.id.toString()));
    console.log(viemChains);
    const config = createConfig({
        chains: [...viemChains],
        multiInjectedProviderDiscovery: true,
        autoConnect: true,
        _connectors: [
            coinbaseWallet({ appName: 'wagmi' }),
            walletConnect({
                showQrModal: true,
                projectId: '0908f6dccf88caf94e3bfa07b199620c',
                ...walletConnectConfig,
                metadata: {
                    name: 'Launchpad',
                    description: 'Decentralized Crypto Launchpad',
                    url: 'https://www.launcho.io',
                    icons: ['https://www.launcho.io/icon.png'],
                    ...walletConnectConfig.metadata
                },

            }),
        ],
        get connectors() {
            return this._connectors;
        },
        set connectors(value) {
            this._connectors = value;
        },
        transports: chains.reduce((memo, chn) => {
            memo[parseInt(chn.chainId)] = fallback([
                ...chn.websockets.map(w => webSocket(w)),
                ...chn.https.map(h => http(h)),
            ]);
            return memo;
        }, {}),
        /* client({ chain }) {
            const active = chains.find(c => c.chainId.toString() === chain.id.toString());
            return createClient({
                chain,
                transport: fallback([
                    ...active.websockets.map(w => webSocket(w)),
                    ...active.https.map(h => http(h)),
                ])
            });
        }, */
    });
    return config;
};
