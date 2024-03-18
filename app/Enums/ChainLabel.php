<?php

namespace App\Enums;

enum ChainLabel: string
{
    case Bitcoin = 'Bitcoin';
    case BitcoinTestnet = 'Bitcoin Testnet';
    case Dash = 'Dash';
    case DashTestnet = 'DashTestnet';
    case Dogecoin = 'Dogecoin';
    case DogecoinTestnet = 'Dogecoin Testnet';
    case Litecoin = 'Litecoin';
    case LitecoinTestnet = 'Litecoin Testnet';
    case Viacoin = 'Viacoin';
    case Ethereum = 'Ethereum';
    case Ropsten = 'Ropsten';
    case ViacoinTestnet = 'Viacoin Testnet';
    case Binance = 'Binance';
    case BinanceTestnet = 'Binance Testnet';
    case Rinkeby = 'Rinkeby';
    case Goerli = 'Goerli';
    case Kovan = 'Kovan';
    case Optimism = 'Optimism';
    case Arbitrum = 'Arbitrum';
    case OptimisticKovan = 'Optimistic Kovan';
    case ArbitrumRinkeby = 'Arbitrum Rinkeby';
    case Polygon = 'Polygon';
    case PolygonMumbai = 'Polygon Mumbai';
    case bobaNetworkRinkebyTestnet = 'Boba Network Rinkeby Testnet';
    case syscoinMainnet = 'Syscoin Mainnet';
    case gnosis = 'Gnosis';
    case fantomOpera = 'Fantom Opera';
    case bobaNetwork = 'Boba Network';
    case moonbeam = 'Moonbeam';
    case moonriver = 'Moonriver';
    case moonbaseAlpha = 'Moonbase Alpha';
    case kavaEvmTestnet = 'Kava EVM Testnet';
    case kavaEvm = 'Kava EVM';
    case fantomTestnet = 'Fantom Testnet';
    case ioTeXNetworkMainnet = 'IoTeX Network Mainnet';
    case ioTeXNetworkTestnet = 'IoTeX Network Testnet';
    case syscoinTanenbaumTestnet = 'Syscoin Tanenbaum Testnet';
    case celoMainnet = 'Celo Mainnet';
    case avalancheFujiTestnet = 'Avalanche Fuji Testnet';
    case avalancheCChain = 'Avalanche C-Chain';
    case celoAlfajoresTestnet = 'Celo Alfajores Testnet';
    case neonEvmDevNet = 'Neon EVM DevNet';
    case neonEvmMainNet = 'Neon EVM MainNet';
    case harmonyMainnetShard0 = 'Harmony Mainnet Shard 0';
    case harmonyTestnetShard0 = 'Harmony Testnet Shard 0';
    case cronosMainnetBeta = 'cro';
    case telosEvmMainnet = 'Telos EVM';
    case telosEvmTestnet = 'Telos EVM Testnet';
    case hooSmartChain = 'hsc';
    case fuseMainnet = 'fuse';
    case huobiEcoChainMainnet = 'heco';
    case auroraMainnet = 'aurora';
    case auroraTestnet = 'aurora-testnet';
    case metis = 'metis';
}
