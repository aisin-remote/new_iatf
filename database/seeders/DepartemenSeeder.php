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
            'code' => 'HR-IRL-GA',
            'nama_departemen' => 'HR-IRL-GA'
        ]);
        Departemen::create([
            'code' => 'QAS',
            'nama_departemen' => 'Quality Body'
        ]);
        Departemen::create([
            'code' => 'QAS',
            'nama_departemen' => 'Quality Unit'
        ]);
        Departemen::create([
            'code' => 'QAS',
            'nama_departemen' => 'Quality Electric'
        ]);
        Departemen::create([
            'code' => 'PPIC',
            'nama_departemen' => 'PPIC Receiving'
        ]);
        Departemen::create([
            'code' => 'PPIC',
            'nama_departemen' => 'PPIC Delivery'
        ]);
        Departemen::create([
            'code' => 'PPIC',
            'nama_departemen' => 'PPIC Electric'
        ]);
        Departemen::create([
            'code' => 'ENG',
            'nama_departemen' => 'Engineering Body'
        ]);
        Departemen::create([
            'code' => 'ENG',
            'nama_departemen' => 'Engineering Unit'
        ]);
        Departemen::create([
            'code' => 'ENG',
            'nama_departemen' => 'Engineering Electric'
        ]);
        Departemen::create([
            'code' => 'MTE',
            'nama_departemen' => 'Maintenance'
        ]);
        Departemen::create([
            'code' => 'MTE',
            'nama_departemen' => 'Maintenance Electric'
        ]);
        Departemen::create([
            'code' => 'PRD',
            'nama_departemen' => 'Production Unit'
        ]);
        Departemen::create([
            'code' => 'PRD',
            'nama_departemen' => 'Production Body'
        ]);
        Departemen::create([
            'code' => 'PRD',
            'nama_departemen' => 'Production Electric'
        ]);
        Departemen::create([
            'code' => 'PSD',
            'nama_departemen' => 'Production System Development'
        ]);
        Departemen::create([
            'code' => 'ITD',
            'nama_departemen' => 'IT Development'
        ]);
        Departemen::create([
            'code' => 'MS',
            'nama_departemen' => 'Management System'
        ]);
        Departemen::create([
            'code' => 'MR',
            'nama_departemen' => 'Management Representative'
        ]);
    }
}
