<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Departemen::create([
            'nama_departemen' => 'HR-IRL-GA'
        ]);
        Departemen::create([
            'nama_departemen' => 'Quality Body'
        ]);
        Departemen::create([
            'nama_departemen' => 'Quality Unit'
        ]);
        Departemen::create([
            'nama_departemen' => 'Quality Electric'
        ]);
        Departemen::create([
            'nama_departemen' => 'PPIC Receiving'
        ]);
        Departemen::create([
            'nama_departemen' => 'PPIC Delivery'
        ]);
        Departemen::create([
            'nama_departemen' => 'PPIC Electric'
        ]);
        Departemen::create([
            'nama_departemen' => 'Engineering Body'
        ]);
        Departemen::create([
            'nama_departemen' => 'Engineering Unit'
        ]);
        Departemen::create([
            'nama_departemen' => 'Engineering Electric'
        ]);
        Departemen::create([
            'nama_departemen' => 'Maintenance'
        ]);
        Departemen::create([
            'nama_departemen' => 'Maintenance Electric'
        ]);
        Departemen::create([
            'nama_departemen' => 'Production Unit'
        ]);
        Departemen::create([
            'nama_departemen' => 'Production Body'
        ]);
        Departemen::create([
            'nama_departemen' => 'Production Electric'
        ]);
        Departemen::create([
            'nama_departemen' => 'Production Development System'
        ]);
        Departemen::create([
            'nama_departemen' => 'IT Development'
        ]);
        Departemen::create([
            'nama_departemen' => 'Management System'
        ]);
    }
}
