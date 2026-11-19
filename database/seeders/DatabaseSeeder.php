<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles and permissions are installed by the migration so first-run registration works.
        // Do not create a known-password admin account in production or development.
    }
}
