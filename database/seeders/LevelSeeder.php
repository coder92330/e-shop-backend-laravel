<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\level_model;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        level_model::create([
            'id' => 1,
            'name' => "SuperAdmin",
        ]);

        $this->command->info('Levels table seeded.');
    }
}
