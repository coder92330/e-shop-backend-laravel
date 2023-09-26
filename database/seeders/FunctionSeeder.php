<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\function_model;

class FunctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = collect([
            'member', 'goods', 'job', 'proxy', 'browser', 'login', 'machine', 'surfing', 'history', 'log', 'operation'
        ]);
        foreach($pages as $page){
            if($page !== 'history' && $page !== 'operation' && $page !== 'log'){
                function_model::create([
                    'page' => $page,
                    'operation' => 'view',
                ]);
                function_model::create([
                    'page' => $page,
                    'operation' => 'add',
                ]);
                function_model::create([
                    'page' => $page,
                    'operation' => 'edit',
                ]);
                function_model::create([
                    'page' => $page,
                    'operation' => 'del',
                ]);
            } else {
                function_model::create([
                    'page' => $page,
                    'operation' => 'view',
                ]);
            }
        }
    }
}
