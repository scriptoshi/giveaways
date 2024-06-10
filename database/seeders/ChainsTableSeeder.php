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
                'https' => ['https://rpc.ankr.com/arbitrum/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'https://rpc.ankr.com/arbitrum', 'https://arbitrum-one.public.blastapi.io'],
                'websockets' => ['wss://rpc.ankr.com/arbitrum/ws/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'wss://avalanche-c-chain-rpc.publicnode.com', 'wss://arbitrum-one-rpc.publicnode.com'],
            ], [
                'name' => 'Arbitrum Nova',
                'slug' => 'arbitrumnova',
                'chainId' => '42170',
                'explorer' => 'https://nova.arbiscan.io/',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/arbitrumnova/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'https://arbitrum-nova.public.blastapi.io', 'https://nova.arbitrum.io/rpc'],
                'websockets' => ['wss://rpc.ankr.com/arbitrumnova/ws/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'wss://arbitrum-nova.publicnode.com', 'wss://arbitrum-nova-rpc.publicnode.com'],
            ], [
                'name' => 'Avalanche',
                'slug' => 'avalanche',
                'chainId' => '43114',
                'explorer' => 'https://snowtrace.io',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/avalanche/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'https://rpc.ankr.com/avalanche', 'https://rpc.tornadoeth.cash/avax'],
                'websockets' => ['wss://rpc.ankr.com/avalanche/ws/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'wss://arbitrum-one.publicnode.com'],
            ], [
                'name' => 'polygon',
                'slug' => 'polygon',
                'chainId' => '137',
                'explorer' => 'https://polygonscan.com',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/polygon/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'https://rpc.ankr.com/polygon', 'https://polygon-mainnet.public.blastapi.io'],
                'websockets' => ['wss://rpc.ankr.com/polygon/ws/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'wss://polygon-bor-rpc.publicnode.com'],
            ],
            [
                'name' => 'Binance Smart Chain',
                'slug' => 'bsc',
                'chainId' => '56',
                'explorer' => 'https://www.bscscan.com/',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/bsc/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'https://rpc.ankr.com/bsc', 'https://binance.llamarpc.com', 'https://bsc-dataseed1.defibit.io'],
                'websockets' => ['wss://rpc.ankr.com/bsc/ws/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'wss://bsc-rpc.publicnode.com'],
            ],
            [
                'name' => 'Fantom',
                'slug' => 'fantom',
                'chainId' => '250',
                'explorer' => 'https://ftmscan.com',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/fantom/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'https://fantom.drpc.org', 'https://rpc.ankr.com/fantom', 'https://rpc3.fantom.network'],
                'websockets' => ['wss://rpc.ankr.com/fantom/ws/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'wss://fantom-rpc.publicnode.com'],
            ],
            [
                'name' => 'Optimism',
                'slug' => 'optimism',
                'chainId' => '10',
                'explorer' => 'https://optimistic.etherscan.io/',
                'testnet' => '0',
                'active' => '1',
                'https' => ['https://rpc.ankr.com/optimism/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'https://mainnet.optimism.io', 'https://optimism.gateway.tenderly.co', 'https://rpc.ankr.com/optimism'],
                'websockets' => ['wss://rpc.ankr.com/optimism/ws/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'wss://optimism-rpc.publicnode.com'],
            ],
            [
                'name' => 'Zksync Era',
                'slug' => 'zksync',
                'chainId' => '324',
                'explorer' => 'https://explorer.zksync.io/',
                'testnet' => '0',
                'active' => '0',
                'https' => ['https://mainnet.era.zksync.io', 'https://zksync-era.blockpi.network/v1/rpc/public', 'https://1rpc.io/zksync2-era'],
                'websockets' => [],
            ],
            [
                'name' => 'Sepolia Testnet',
                'slug' => 'ethereum',
                'chainId' => '11155111',
                'explorer' => 'https://sepolia.etherscan.io/',
                'testnet' => '1',
                'active' => '0',
                'https' => ['https://rpc.ankr.com/eth_sepolia/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'https://rpc2.sepolia.org', 'https://rpc.sepolia.org', 'https://eth-sepolia.public.blastapi.io'],
                'websockets' => ['wss://rpc.ankr.com/eth_sepolia/ws/a2e2e0ee70153e9f9ea6eca45dbdce42021037389167d0e56825030f04213d1c', 'wss://sepolia.gateway.tenderly.co', 'wss://ethereum-sepolia-rpc.publicnode.com'],
            ],
            [
                'name' => 'Bsc Testnet',
                'slug' => 'bsc',
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
