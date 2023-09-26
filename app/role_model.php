<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class role_model extends Model
{
    use HasFactory;
    protected $fillable = [
        'level_id',
        'function_id',
        'permission'
    ];
    protected $table="roles";

    public function getLevelPermission($level_id, $function_id)
    {
        $permission = DB::table('roles')->where([
            ['level_id', $level_id],
            ['function_id', $function_id]
        ])->first();
        if(json_encode($permission) === "null"){
            return [
                'status' => 425,
                'message' => "No exist"
            ];
        } else {
            return [
                'status' => 201,
                'level_id' => $permission->level_id,
                'function_id' => $permission->function_id,
                'permission' => $permission->permission,
            ];
        }
    }
    public function getLevelPermissionList($level_id)
    {
        $permissions = DB::table('roles')->where('level_id', $level_id)->get();
        if(json_encode($permissions) === "null"){
            return [
                'status' => 425,
                'message' => "No exist"
            ];
        } else {
            return [
                'status' => 201,
                'permissions' => $permissions,
            ];
        }
    }
    public function setLevelPermission($level_id, $function_id, $permission)
    {
        if($level_id > 1){
            $role = $this->where([
                ['level_id', $level_id],
                ['function_id', $function_id]
            ])->firstOrFail();
            if(json_encode($role) === "{}"){
                return [
                    'status' => 425,
                    'message' => "No exist"
                ];
            } else {
                $upperrole = $this->where([
                    ['level_id', $level_id - 1],
                    ['function_id', $function_id]
                ])->firstOrFail();
                if($upperrole->permission != 0){
                    $role->permission = $permission;
                    $role->save();
                }
                return [
                    'status' => 201
                ];
            }
        } else {
            return [
                'status' => 201
            ];
        }
    }
    public function setLevelPermissionList($level_id, $permission_list)
    {
        $roles = $this->where('level_id', $level_id)->get();
        if(json_encode($roles) === "{}"){
            return [
                'status' => 425,
                'message' => "No exist"
            ];
        } else {
            foreach($roles as $role){
                $this->setLevelPermission($level_id, $role->function_id, $permission_list[$role->function_id-1]);
            }
            return [
                'status' => 201
            ];
        }
    }
    public function blockLevelPermission($level_id)
    {
        $deleteRoles = DB::table('roles')->where('level_id', $level_id)->delete();
        return $deleteRoles;
    }
    public function blockChildLevelPermission($level_id)
    {
        $childRoles = $this->where('level_id', '>', $level_id)->delete();
        return $childRoles;
    }
    public function add()
    {
        for($i = 0 ; $i < 35 ; $i++){ 
            $this->create([ 
                'level_id'=> 1,
                'function_id' => $i+1,
                'permission' => 1,
            ]);
        }
        return [
            'status' => 201,
            'message' => 'Resource created.'
        ];
    }
}
