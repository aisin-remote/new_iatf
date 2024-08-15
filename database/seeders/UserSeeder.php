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
            'password' => bcrypt('12345678'),
        ]);
        $admin->assignRole('admin');

        $admin2 = User::create([
            'npk' => '111111',
            'name' => 'Fina',
            'password' => bcrypt('12345678'),
        ]);
        $admin2->assignRole('admin');


        $guest = User::create([
            'npk' => '000001',
            'name' => 'Udin',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000002',
            'name' => 'Asep',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000003',
            'name' => 'Joko',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000004',
            'name' => 'Yono',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000005',
            'name' => 'Dika',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000006',
            'name' => 'Budi',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000007',
            'name' => 'Yudi',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000008',
            'name' => 'Ikhsan',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000009',
            'name' => 'Risky',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000010',
            'name' => 'Rates',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000011',
            'name' => 'Fabojo',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000012',
            'name' => 'Umar',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000013',
            'name' => 'Ali',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000014',
            'name' => 'Amin',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000015',
            'name' => 'Jaka',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000016',
            'name' => 'Amin',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000017',
            'name' => 'Iman',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');

        $guest = User::create([
            'npk' => '000018',
            'name' => 'Aji',
            'password' => bcrypt('12345678'),
        ]);
        $guest->assignRole('guest');
    }
}
