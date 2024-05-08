<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'HR & GA',
                'password' => Hash::make('12345678'), // Menggunakan Hash::make() untuk menyandikan password
                'role' => 'departemen',
                'departemen' => 'HR & GA', // Ganti 1 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'IR & LEGAL',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'IR & LEGAL', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'MARKETING',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'MARKETING', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'FINANCE & ACCOUNTING',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'FINANCE & ACCOUNTING', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'PURCHASING & EXIM',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'PURCHASING & EXIM', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'NEW PROJECT & LOCALIZATION',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'NEW PROJECT & LOCALIZATION', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'BODY COMPONENT',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'BODY COMPONENT', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'PROD UNIT MACHINING',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'PROD UNIT MACHINING', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'PPIC',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'PPIC', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ENGINERING BODY',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'ENGINERING BODY', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ENGINERING UNIT',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'ENGINERING UNIT', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'MAINTENANCE',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'MAINTENANCE', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'QA BODY COMPONENT',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'QA BODY COMPONENT', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'MANAGEMENT SYSTEM',
                'password' => Hash::make('12345678'),
                'role' => 'MANAGEMENT SYSTEM',
                'departemen' => 'admin', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'QA ENGINE COMPONENT',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'QA ENGINE COMPONENT', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'IT DEVELOPMENT',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'IT DEVELOPMENT', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'PRODUCTION SYSTEM & DEVELOPMENT',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'PRODUCTION SYSTEM & DEVELOPMENT', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'PROD UNIT DC',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'PROD UNIT DC', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ENGINEERING & QUALITY ELECTRICAL COMPONENT',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'ENGINEERING & QUALITY ELECTRICAL COMPONENT', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'PPIC ELECTRIC',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'PPIC ELECTRIC', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'PRODUCTION ELECTRIC',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'PRODUCTION ELECTRIC', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'MAINTENANCE ELECTRIC',
                'password' => Hash::make('12345678'),
                'role' => 'departemen',
                'departemen' => 'MAINTENANCE ELECTRIC', // Ganti 2 dengan ID departemen yang sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambahkan data user lainnya sesuai kebutuhan
        ]);
    }
}
