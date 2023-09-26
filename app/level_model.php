<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class level_model extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name'
    ];
    protected $table="levels";
    
    public function add($id, $name)
    {
        $oldLevel1 = DB::table('levels')->where('id', $id)->first();
        $oldLevel2 = DB::table('levels')->where('name', $name)->first();
        if(json_encode($oldLevel1) !== 'null' || json_encode($oldLevel2) !== 'null'){
            return [
                'status' => 425,
                'message' => $this->getLastError("unique")
            ];
        } else {
            $level = $this->create([
                'id' => $id,
                'name' => $name,
            ]);
            $roleModel=new role_model();
            for($i = 0 ; $i < 35 ; $i++){ 
                $roleModel->create([ 
                    'level_id'=> $id,
                    'function_id' => $i+1,
                    'permission' => 0,
                ]);
            }
            return [
                'status' => 201,
                'message' => 'Resource created.',
                'id' => $id
            ];
        }
        
    }
    public function setName($id, $name)
    {
        $oldLevel = DB::table('levels')->where('id', $id)->first();
        $level = $this->where("id", $id)->firstOrFail();
        if(json_encode($oldLevel) === "null"){
            return [
                'status' => 425,
                'message' => $this->getLastError("noexist")
            ];
        }
        $otherLevel = DB::table('levels')->where('name', $name)->first();
        if(json_encode($otherLevel) !== "null"){
            return [
                'status' => 425,
                'message' => $this->getLastError("unique")
            ];
        }
        $level->name = $name;
        $level->save();
        return [
            'status' => 201,
            'message' => 'Resource edited.',
            'id' => $id
        ];
    }
    public function del($id)
    {
        $oldLevel = DB::table('levels')->where('id', $id)->first();
        if(json_encode($oldLevel) === "null"){
            return [
                'status' => 425,
                'message' => $this->getLastError("noexist")
            ];
        } else {
            $roleModel=new role_model();
            $memberModel=new member_model();
            $childMembers = $memberModel->where('id', '>=', $id)->delete();
            $childLevels = $this->where('id', '>', $id)->delete();
            $roleModel->blockLevelPermission($id);
            $roleModel->blockChildLevelPermission($id);
            $level = $this->where("id", $id)->firstOrFail();
            $level->delete();
            return [
                'status' => 201,
                'message' => 'Resource deleted.',
                'id' => $id
            ];
        }
    }
    public function getName($id)
    {
        $oldLevel = DB::table('levels')->where('id', $id)->first();
        if(json_encode($oldLevel) === "null"){
            return [
                'status' => 425,
                'message' => $this->getLastError("noexist")
            ];
        } else {
            return [
                'status' => 201,
                'name' => $oldLevel->name
            ];
        }
    }
    public function getIdByName($name)
    {
        $oldLevel = DB::table('levels')->where('name', $name)->first();
        if(json_encode($oldLevel) === "null"){
            return [
                'status' => 425,
                'message' => $this->getLastError("noexist")
            ];
        } else {
            return [
                'status' => 201,
                'id' => $oldLevel->id
            ];
        }
    }
    public function getParentLevel($id)
    {
        $oldLevel = DB::table('levels')->where('id', '<', $id)->orderBy('id', 'desc')->first();
        echo(json_encode($oldLevel));
        if(json_encode($oldLevel) === "null"){
            return [
                'status' => 425,
                'message' => $this->getLastError("noexist")
            ];
        } else {
            return [
                'status' => 201,
                'id' => $oldLevel->id
            ];
        }
    }
    public function getLevelList(){
        $levels = DB::table('levels')
            ->get();
        return $levels;
    }
    public function getLevelById($id){
        $level = DB::table('levels')
            ->join('roles', 'levels.id', '=', 'roles.level_id')
            ->join('functions', 'roles.function_id', '=', 'functions.id')
            ->select('levels.*', 'roles.*', 'functions.*')
            ->where('levels.id', $id)
            ->get();
        return $level;
    }
    public function getParentLevelById($id){
        if($id !== 0) {
            return DB::table('levels')
                ->join('roles', 'levels.id', '=', 'roles.level_id')
                ->join('functions', 'roles.function_id', '=', 'functions.id')
                ->select('levels.*', 'roles.*', 'functions.*')
                ->where('levels.id', $id-1)
                ->get();
        }
        return [];
    }
    public function getLastError($error_type)
    {
        switch ($error_type) {
            case 'unique':
                return "Already exist.";
                break;
            case 'noexist':
                return "Data is not exist.";
                break;
                
            default:
                return "Some problem happened.";
                break;
        }
    }
}
