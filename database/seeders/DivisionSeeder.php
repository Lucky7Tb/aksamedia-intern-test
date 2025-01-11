<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Division::create(['name' => 'Mobile Apps']);
        Division::create(['name' => 'QA']);
        Division::create(['name' => 'Full Stack']);
        Division::create(['name' => 'Backend']);
        Division::create(['name' => 'Frontend']);
        Division::create(['name' => 'UI/UX Designer']);
    }
}
