<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Systematically seed Companies, Departments, Employees, and Users
        $this->call(SystematicSeeder::class);
        
        // Seed IT Toolbox Topics and Scheduled Talks
        $this->call(ITToolboxTopicsSeeder::class);
    }
}
