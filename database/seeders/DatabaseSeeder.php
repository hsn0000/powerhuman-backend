<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\Company;
use \App\Models\Team;
use \App\Models\Role;
use \App\Models\Responsibility;
use \App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        // \App\Models\User::factory()->create([
            //     'name' => 'Test User',
            //     'email' => 'test@example.com',
            // ]);
            
        User::create([
            'name' =>'Super Admin',
            'email' => 'supersu@yopmail.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);

        User::factory(10)->create();
        Company::factory(10)->create();
        Team::factory(30)->create();
        Role::factory(50)->create();
        Responsibility::factory(50)->create();
        Employee::factory(1000)->create();

        $this->call([
            UserCompanySeeder::class,
        ]);
    }
}
