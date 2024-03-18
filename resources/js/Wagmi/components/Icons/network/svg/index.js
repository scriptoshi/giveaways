// eslint-disable-next-line import/order


import ArbitrumNova from "./arbitrum-nova.svg?url";
import Arbitrum from "./arbitrum.svg?url";
import Aurora from "./aurora.svg?url";
import Avax from "./avax.svg?url";
import Base from "./base.svg?url";
import Binance from "./binance.svg?url";
import Boba from "./boba.svg?url";
import Celo from "./celo.svg?url";
import Cronos from "./cronos.svg?url";
import Ethereum from "./ethereum.svg?url";
import Ftm from "./ftm.svg?url";
import Fuze from "./fuse.svg?url";
import Gnosis from "./gnosis.svg?url";
import Harmony from "./harmony.svg?url";
import Huobi from "./huobi.svg?url";
import Kava from "./kava.svg?url";
import Moonbeam from "./moonbeam.svg?url";
import Moonriver from "./moonriver.svg?url";
import Okex from "./okex.svg?url";
import Optimism from "./optimism.svg?url";
import Palm from "./palm.svg?url";
import Polygon from "./polygon.svg?url";
import Telos from "./telos.png?url";
import Zksync from "./zksync.svg?url";
const ChainId = Object.freeze({
    MAINNET: 1,
    GOERLI: 5,
    BASE: 8453,
    ARBITRUM_ONE: 42161,
    ARBITRUM_NOVA: 42170,
    OPTIMISM: 10,
    BINANCE: 56,
    BINANCE_TESTNET: 97,
    ZKSYNC: 324,
    ZKSYNC_GOERLI: 280,
    POLYGON_ZKEVM: 1101,
    POLYGON: 137,
    AVALANCHE: 43114,
    FANTOM: 250,
    SEPOLIA: 11155111,
    ARBITRUM_GOERLI: 421613,
    OPTIMISTIC_KOVAN: 69,
    OPTIMISTIC_GOERLI: 420,
    POLYGON_MUMBAI: 80001,
    AVALANCHE_FUJI: 43113,
    CELO: 42220,
    CELO_ALFAJORES: 44787,
    FANTOM_TESTNET: 4002,
    GNOSIS: 100,
    HARMONY: 1666600000,
    HARMONY_TESTNET: 1666700000,
    IOTEX: 4689,
    IOTEX_TESTNET: 4690,
    MOONBEAM: 1284,
    MOONBASE: 1287,
    KAVA: 2222,
    KAVA_TESTNET: 2221,
    SYSCOIN: 57,
    SYSCOIN_TESTNET: 5700,
    BOBA: 288,
    BOBA_GOERLI: 2888,
    MOONRIVER: 1285,
    AURORA: 1313161554,
    AURORA_TESTNET: 1313161555,
    CRONOS: 25,
    CRONOS_TESTNET: 338,
    FUSE: 122,
    HECO: 128,
    HECO_TESTNET: 256,
    OKEX: 66,
    OKEX_TESTNET: 65,
    HOO: 70,
    TELOS: 40,
    TELOS_TESTNET: 41,
    PALM: 11297108109,
    PALM_TESTNET: 11297108099,
    METIS: 1088,
    METIS_TESTNET: 588,
});
export const NETWORK_NAKED_ICON = {
    [ChainId.MAINNET]: Ethereum,
    [ChainId.BASE]: Base,
    [ChainId.ZKSYNC]: Zksync,
    [ChainId.ZKSYNC_GOERLI]: Zksync,
    [ChainId.SEPOLIA]: Ethereum,
    [ChainId.GOERLI]: Ethereum,
    [ChainId.FANTOM]: Ftm,
    [ChainId.FANTOM_TESTNET]: Ftm,
    [ChainId.CRONOS]: Cronos,
    [ChainId.CRONOS_TESTNET]: Cronos,
    [ChainId.AURORA]: Aurora,
    [ChainId.AURORA_TESTNET]: Aurora,
    [ChainId.POLYGON]: Polygon,
    [ChainId.POLYGON_MUMBAI]: Polygon,
    [ChainId.GNOSIS]: Gnosis,
    [ChainId.BINANCE]: Binance,
    [ChainId.BINANCE_TESTNET]: Binance,
    [ChainId.ARBITRUM_ONE]: Arbitrum,
    [ChainId.ARBITRUM_GOERLI]: Arbitrum,
    [ChainId.AVALANCHE]: Avax,
    [ChainId.AVALANCHE_FUJI]: Avax,
    [ChainId.HARMONY]: Harmony,
    [ChainId.HARMONY_TESTNET]: Harmony,
    [ChainId.CELO]: Celo,
    [ChainId.CELO_ALFAJORES]: Celo,
    [ChainId.MOONRIVER]: Moonriver,
    [ChainId.MOONBASE]: Moonriver,
    [ChainId.FUSE]: Fuze,
    [ChainId.TELOS]: Telos,
    [ChainId.TELOS_TESTNET]: Telos,
    [ChainId.MOONBEAM]: Moonbeam,
    [ChainId.OPTIMISM]: Optimism,
    [ChainId.OPTIMISTIC_GOERLI]: Optimism,
    // [ChainId.METIS]: Metis,
    // [ChainId.METIS_TESTNET]: Metis,
    [ChainId.KAVA]: Kava,
    [ChainId.KAVA_TESTNET]: Kava,
    [ChainId.ARBITRUM_NOVA]: ArbitrumNova,
    [ChainId.HECO]: Huobi,
    [ChainId.HECO_TESTNET]: Huobi,
    [ChainId.OKEX]: Okex,
    [ChainId.OKEX_TESTNET]: Okex,
    [ChainId.PALM]: Palm,
    [ChainId.BOBA]: Boba,
    [ChainId.BOBA_GOERLI]: Boba,
};
