<?php

namespace Database\Seeders;

use App\Models\Factory;
use File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestFactoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $facoriesJson = json_decode(File::get(database_path('seeders/factories.json')), true);
        foreach ($facoriesJson as $factory) {
            Factory::updateOrCreate(
                [
                    'chainId' => $factory['chainId'],
                    'contract' => $factory['contract'],
                ],
                [
                    ...$factory,
                    'abi' => json_decode($factory['abi']),
                    'factory_abi' => json_decode($factory['factory_abi']),
                ]
            );
        }
    }
}
