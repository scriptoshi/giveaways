<?php

namespace Database\Seeders;

use App\Models\User;
use File;
use Hash;
use Illuminate\Database\Seeder;
use Str;

class SleepUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = json_decode(File::get(database_path('seeders/users.json')));

        foreach ($users as  $user) {
            $u = User::query()->firstOrCreate([
                'email' => $user->email
            ], [
                'name' => $user->name,
                'email_verified_at' => $user->email_verified_at,
                'referral' => $user->referral,
                'password' => Hash::make(Str::random(16))
            ]);
            $u->accounts()->create([
                'provider' => $user->provider,
                'address' => $user->address,
            ]);
        }
    }
}
