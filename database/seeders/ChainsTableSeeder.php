<?php

namespace Database\Seeders;

use App\Models\Chain;
use Illuminate\Database\Seeder;

class ChainsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chains = [
            [
                'name' => 'Arbitrum One',
                'slug' => 'arbitrum',
                'chainId' => '42161',
                'explorer' => 'https://arbiscan.io/',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/arbitrum', 'https://arbitrum-one.public.blastapi.io'],
                'websockets' => ['wss://avalanche-c-chain-rpc.publicnode.com', 'wss://arbitrum-one-rpc.publicnode.com'],
            ], [
                'name' => 'Arbitrum Nova',
                'slug' => 'nova',
                'chainId' => '42170',
                'explorer' => 'https://nova.arbiscan.io/',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://arbitrum-nova.public.blastapi.io', 'https://nova.arbitrum.io/rpc'],
                'websockets' => ['wss://arbitrum-nova.publicnode.com', 'wss://arbitrum-nova-rpc.publicnode.com'],
            ], [
                'name' => 'Avalanche',
                'slug' => 'avax',
                'chainId' => '43114',
                'explorer' => 'https://snowtrace.io',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/avalanche', 'https://rpc.tornadoeth.cash/avax'],
                'websockets' => ['wss://arbitrum-one.publicnode.com'],
            ], [
                'name' => 'polygon',
                'slug' => 'matic',
                'chainId' => '137',
                'explorer' => 'https://polygonscan.com',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/polygon', 'https://polygon-mainnet.public.blastapi.io'],
                'websockets' => ['wss://polygon-bor-rpc.publicnode.com'],
            ],
            [
                'name' => 'Binance Smart Chain',
                'slug' => 'bsc',
                'chainId' => '56',
                'explorer' => 'https://www.bscscan.com/',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/bsc', 'https://binance.llamarpc.com', 'https://bsc-dataseed1.defibit.io'],
                'websockets' => ['wss://bsc-rpc.publicnode.com'],
            ],
            [
                'name' => 'Fantom',
                'slug' => 'ftm',
                'chainId' => '250',
                'explorer' => 'https://ftmscan.com',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://fantom.drpc.org', 'https://rpc.ankr.com/fantom', 'https://rpc3.fantom.network'],
                'websockets' => ['wss://fantom-rpc.publicnode.com'],
            ],
            [
                'name' => 'Optimism',
                'slug' => 'optimism',
                'chainId' => '10',
                'explorer' => 'https://optimistic.etherscan.io/',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://mainnet.optimism.io', 'https://optimism.gateway.tenderly.co', 'https://rpc.ankr.com/optimism'],
                'websockets' => ['wss://optimism-rpc.publicnode.com'],
            ],
            [
                'name' => 'Zksync Era',
                'slug' => 'zksync-era',
                'chainId' => '324',
                'explorer' => 'https://explorer.zksync.io/',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://mainnet.era.zksync.io', 'https://zksync-era.blockpi.network/v1/rpc/public', 'https://1rpc.io/zksync2-era'],
                'websockets' => [],
            ],
            [
                'name' => 'Sepolia Testnet',
                'slug' => 'ethereum',
                'chainId' => '11155111',
                'explorer' => 'https://sepolia.etherscan.io/',
                'testnet' => '1',
                'active' => '1',
                'https' => ['https://rpc2.sepolia.org', 'https://rpc.sepolia.org', 'https://eth-sepolia.public.blastapi.io'],
                'websockets' => ['wss://sepolia.gateway.tenderly.co', 'wss://ethereum-sepolia-rpc.publicnode.com'],
            ],
            [
                'name' => 'Bsc Testnet',
                'slug' => 'bsc-testnet',
                'chainId' => '97',
                'explorer' => 'https://testnet.bscscan.com/',
                'testnet' => '1',
                'active' => '1',
                'https' => ['https://bsc-testnet-rpc.publicnode.com', 'https://bsc-testnet.public.blastapi.io', 'https://public.stackup.sh/api/v1/node/bsc-testnet'],
                'websockets' => ['wss://bsc-testnet-rpc.publicnode.com',],
            ]
        ];
        foreach ($chains as $chain) {
            Chain::updateOrCreate(
                [
                    'chainId' => $chain['chainId'],
                ],
                [
                    ...$chain,
                    //'https' => json_encode($chain['https']),
                    //'websockets' => json_encode($chain['websockets'])
                ]
            );
        }
    }
}
