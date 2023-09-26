<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class function_model extends Model
{
    use HasFactory;
    protected $fillable = [
        'page',
        'operation'
    ];
    protected $table="functions";

    public function getIdByName($page, $operation)
    {
        $func = DB::table('functions')->where([
            ['page', $page],
            ['operation', $operation]
        ])->first();
        if(json_encode($func) === "null"){
            return [
                'status' => 425,
                'message' => "No exist"
            ];
        } else {
            return [
                'status' => 201,
                'id' => $func->id
            ];
        }
    }
    public function getById($id)
    {
        $func = DB::table('functions')->where('id', $id)->first();
        if(json_encode($func) === "null"){
            return [
                'status' => 425,
                'message' => "No exist"
            ];
        } else {
            return [
                'status' => 201,
                'page' => $func->page,
                'operation' => $func->operation
            ];
        }
    }
    public function getFunctionList()
    {
        $functions = DB::table('functions')->get();
        echo(json_encode($functions));
        return [
            'status' => 201,
            'functions' => $functions
        ];
    }
    public function add()
    {
        $pages = [
            'member', 'goods', 'job', 'proxy', 'browser', 'login', 'machine', 'surfing', 'history', 'log', 'operation'
        ];
        foreach($pages as $page){            
            if($page !== 'history' && $page !== 'operation' && $page !== 'log'){
                $this->create([
                    'page' => $page,
                    'operation' => 'view',
                ]);
                $this->create([
                    'page' => $page,
                    'operation' => 'add',
                ]);
                $this->create([
                    'page' => $page,
                    'operation' => 'edit',
                ]);
                $this->create([
                    'page' => $page,
                    'operation' => 'del',
                ]);
            } else {
                $this->create([
                    'page' => $page,
                    'operation' => 'view',
                ]);
            }
        }
        return [
            'status' => 201,
            'message' => 'Resource created.'
        ];
    }
}
