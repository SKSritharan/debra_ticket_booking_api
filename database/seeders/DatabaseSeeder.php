<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // \App\Models\User::factory(10)->create();

         \App\Models\User::factory()->create([
             'name' => 'Debra Admin',
             'email' => 'admin@debra.com',
             'role_id' => 1,
             'password' => Hash::make('12345678')
         ]);
    }
}
