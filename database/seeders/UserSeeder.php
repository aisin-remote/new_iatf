<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'npk' => '000000',
            'departemen_id' => 18,
            'password' => bcrypt('12345678'),
        ]);
        $admin->assignRole('admin');

        $admin2 = User::create([
            'npk' => '111111',
            'departemen_id' => 17,
            'password' => bcrypt('12345678'),
        ]);
        $admin2->assignRole('admin');


        $guest = User::create([
            'npk' => '222222',
            'departemen_id' => 1,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '333333',
            'departemen_id' => 2,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '444444',
            'departemen_id' => 3,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '555555',
            'departemen_id' => 4,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '666666',
            'departemen_id' => 5,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '777777',
            'departemen_id' => 6,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '888888',
            'departemen_id' => 7,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '999999',
            'departemen_id' => 8,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '111222',
            'departemen_id' => 9,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '111333',
            'departemen_id' => 10,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '111444',
            'departemen_id' => 11,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '111555',
            'departemen_id' => 12,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '111666',
            'departemen_id' => 13,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '111777',
            'departemen_id' => 14,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '111888',
            'departemen_id' => 15,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '111999',
            'departemen_id' => 16,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '222000',
            'departemen_id' => 17,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '222111',
            'departemen_id' => 18,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');
    }
}
