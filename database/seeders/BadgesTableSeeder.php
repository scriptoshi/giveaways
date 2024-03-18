<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the Badges Table with some Default Badges;
     * @return void
     */
    public function run()
    {
        //
        collect([
            [
                'badge' => 'KYC',
                'description' => 'Team has verified ID Card, Location and Phone',
                'active' => true
            ],
            [
                'badge' => 'SAFU',
                'description' => 'This token contract meets our Safe Standards',
                'active' => true
            ],
            [
                'badge' => 'FEATURE',
                'description' => 'Projects is featured by admin.',
                'active' => true
            ],
            [
                'badge' => 'AUDIT',
                'description' => 'The project contracts have been audited and verified by our team',
                'active' => true
            ]
        ])->each(fn ($bg) => Badge::firstOrCreate(['badge' => $bg['badge'],], $bg));
    }
}
