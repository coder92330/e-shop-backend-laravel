<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class nvlogin_model extends Model
{
    use HasFactory;
    protected $fillable = [
        'user',
        'password'
    ];
    protected $table="nvlogins";
    
    public function listOfNull(){
        $nvloginList = DB::table("nvlogins")->where("job_id", null)->get();
        return [
            'status' => 201,
            'nvloginlist' => $nvloginList
        ];
    }
    public function list($page, $limit){
        $nvloginList = DB::table("nvlogins")->offset($page*$limit)->limit($limit)->get();
        return [
            'status' => 201,
            'nvloginlist' => $nvloginList
        ];
    }
    public function search($info, $page, $limit){
        $cond = collect([]);
        foreach($info as $key => $value){
            $cond.push([$key, 'like', "%".$value."%"]);
        }
        return [
            'status' => 201,
            'nvloginlist' => $this->where(get_object_vars($cond))->offset($page*$limit)->limit($limit)->get()
        ];
    }
    public function get($id){
        $nvlogin = $this->where('id', $id)->firstOrFail();
        return [
            'status' => 201,
            'nvlogin' => $nvlogin
        ];
    }
    public function set($id, $info){
        $nvlogin = $this->where('id', $id)->firstOrFail();
        if($info['user']){ $nvlogin->user = $info['user']; }
        if($info['password']){ $nvlogin->password = $info['password']; }
        $nvlogin->save();
        return [
            'status' => 201,
            "message" => "Edited successfully."
        ];
    }
    public function add($info){
        $this->create([
            'user' => $info['user'],
            'password' => $info['password'],
        ]);
    }
    public function del($id){
        $nvlogin = $this->where('id', $id)->delete();
        // $jobModel = new job_model();
        // $jobModel->unsetLogin($id);
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
}
