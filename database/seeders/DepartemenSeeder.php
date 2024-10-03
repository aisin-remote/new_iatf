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
            'code' => 'AII',
            'nama_departemen' => 'Aisin Indonesia',
            'aliases' => 'AII'
        ]);
        Departemen::create([
            'code' => 'AII',
            'nama_departemen' => 'Marketing (AII)',
            'aliases' => 'Marketing (AII)'
        ]);
        Departemen::create([
            'code' => 'AII',
            'nama_departemen' => 'Human Resource Development (AII)',
            'aliases' => 'HRD (AII)'
        ]);
        Departemen::create([
            'code' => 'AII',
            'nama_departemen' => 'Purchasing Group (AII)',
            'aliases' => 'Purchasing (AII)'
        ]);
        Departemen::create([
            'code' => 'AII',
            'nama_departemen' => 'IRL-GA (AII)',
            'aliases' => 'IRL-GA (AII)'
        ]);
        Departemen::create([
            'code' => 'QAS',
            'nama_departemen' => 'Quality Body',
            'aliases' => 'QA Body'
        ]);
        Departemen::create([
            'code' => 'QAS',
            'nama_departemen' => 'Quality Unit',
            'aliases' => 'QA Unit'
        ]);
        Departemen::create([
            'code' => 'QAS',
            'nama_departemen' => 'Quality Electric',
            'aliases' => 'QA Electric'
        ]);
        Departemen::create([
            'code' => 'PPIC',
            'nama_departemen' => 'PPIC Receiving',
            'aliases' => 'PPIC Receiving'
        ]);
        Departemen::create([
            'code' => 'PPIC',
            'nama_departemen' => 'PPIC Delivery',
            'aliases' => 'PPIC Delivery'
        ]);
        Departemen::create([
            'code' => 'PPIC',
            'nama_departemen' => 'PPIC Electric',
            'aliases' => 'PPIC Electric'
        ]);
        Departemen::create([
            'code' => 'ENG',
            'nama_departemen' => 'Engineering Body',
            'aliases' => 'ENG Body'
        ]);
        Departemen::create([
            'code' => 'ENG',
            'nama_departemen' => 'Engineering Unit',
            'aliases' => 'ENG Unit'
        ]);
        Departemen::create([
            'code' => 'ENG',
            'nama_departemen' => 'Engineering Electric',
            'aliases' => 'ENG Electric'
        ]);
        Departemen::create([
            'code' => 'MTE',
            'nama_departemen' => 'Maintenance',
            'aliases' => 'MTE'
        ]);
        Departemen::create([
            'code' => 'MTE',
            'nama_departemen' => 'Maintenance Electric',
            'aliases' => 'MTE Electric'
        ]);
        Departemen::create([
            'code' => 'PRD',
            'nama_departemen' => 'Production Unit',
            'aliases' => 'PRD Unit'
        ]);
        Departemen::create([
            'code' => 'PRD',
            'nama_departemen' => 'Production Body',
            'aliases' => 'PRD Body'
        ]);
        Departemen::create([
            'code' => 'PRD',
            'nama_departemen' => 'Production Electric',
            'aliases' => 'PRD Electric'
        ]);
        Departemen::create([
            'code' => 'PSD',
            'nama_departemen' => 'Production System Development',
            'aliases' => 'PSD'
        ]);
        Departemen::create([
            'code' => 'ITD',
            'nama_departemen' => 'IT Development',
            'aliases' => 'ITD'
        ]);
        Departemen::create([
            'code' => 'MS',
            'nama_departemen' => 'Management System',
            'aliases' => 'MS'
        ]);
        Departemen::create([
            'code' => 'MR',
            'nama_departemen' => 'Management Representative',
            'aliases' => 'MR'
        ]);
    }
}
