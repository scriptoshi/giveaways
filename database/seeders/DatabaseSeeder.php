<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ChainsTableSeeder::class);
        $this->call(CoinsTableSeeder::class);
        $this->call(BadgesTableSeeder::class);
        $this->call(SleepUsersTableSeeder::class);
    }
}
