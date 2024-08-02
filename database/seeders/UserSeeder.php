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
            'name' => 'Rama',
            'departemen_id' => 18,
            'password' => bcrypt('12345678'),
        ]);
        $admin->assignRole('admin');

        $admin2 = User::create([
            'npk' => '111111',
            'name' => 'Fina',
            'departemen_id' => 18,
            'password' => bcrypt('12345678'),
        ]);
        $admin2->assignRole('admin');


        $guest = User::create([
            'npk' => '000001',
            'name' => 'Udin',
            'departemen_id' => 1,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000002',
            'name' => 'Asep',
            'departemen_id' => 2,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000003',
            'name' => 'Joko',
            'departemen_id' => 3,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000004',
            'name' => 'Yono',
            'departemen_id' => 4,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000005',
            'name' => 'Dika',
            'departemen_id' => 5,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000006',
            'name' => 'Budi',
            'departemen_id' => 6,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000007',
            'name' => 'Yudi',
            'departemen_id' => 7,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000008',
            'name' => 'Ikhsan',
            'departemen_id' => 8,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000009',
            'name' => 'Risky',
            'departemen_id' => 9,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000010',
            'name' => 'Rates',
            'departemen_id' => 10,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000011',
            'name' => 'Fabojo',
            'departemen_id' => 11,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000012',
            'name' => 'Umar',
            'departemen_id' => 12,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000013',
            'name' => 'Ali',
            'departemen_id' => 13,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000014',
            'name' => 'Amin',
            'departemen_id' => 14,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000015',
            'name' => 'Jaka',
            'departemen_id' => 15,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000016',
            'name' => 'Amin',
            'departemen_id' => 16,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000017',
            'name' => 'Iman',
            'departemen_id' => 17,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000018',
            'name' => 'Aji',
            'departemen_id' => 18,
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');
    }
}
