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

        \App\Models\User::factory()->create([
            'name' => 'Debra User',
            'email' => 'user@debra.com',
            'role_id' => 3,
            'password' => Hash::make('12345678')
        ]);

        $user = \App\Models\User::create([
            'name' => 'Debra Partner',
            'email' => 'partner@debra.com',
            'role_id' => 2,
            'password' => Hash::make('12345678')
        ]);

        $partner = $user->partner()->create([
            'contact_number' => '0771234567',
            'company_name' => 'test company'
        ]);
    }
}
