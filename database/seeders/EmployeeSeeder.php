<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $randomDivisionId = Division::select('id')->first();
        Employee::factory(1)->create([
            'division_id' => $randomDivisionId,
        ]);
    }
}
