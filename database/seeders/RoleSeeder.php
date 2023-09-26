<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\role_model;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0 ; $i < 35 ; $i++){
            role_model::create([
                'level_id' => 1,
                'function_id' => $i+1,
                'permission' => 1,
            ]);
        }
    }
}
