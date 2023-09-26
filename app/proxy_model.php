<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class proxy_model extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'port',
        'user',
        'password',
    ];
    protected $table="proxys";
    
    public function list($page, $limit){
        $proxyList = DB::table("proxys")->offset($page*$limit)->limit($limit)->get();
        return [
            'status' => 201,
            'proxylist' => $proxyList
        ];
    }
    public function search($info, $page, $limit){
        $cond = collect([]);
        foreach($info as $key => $value){
            $cond.push([$key, 'like', "%".$value."%"]);
        }
        return [
            'status' => 201,
            'proxylist' => $this->where(get_object_vars($cond))->offset($page*$limit)->limit($limit)->get()
        ];
    }
    public function get($id){
        $proxy = $this->where('id', $id)->firstOrFail();
        return [
            'status' => 201,
            'proxy' => $proxy
        ];
    }
    public function set($id, $info){
        $proxy = $this->where('id', $id)->firstOrFail();
        if($info['ip']){ $proxy->ip = $info['ip']; }
        if($info['port']){ $proxy->port = $info['port']; }
        if($info['user']){ $proxy->user = $info['user']; }
        if($info['password']){ $proxy->password = $info['password']; }
        $proxy->save();
        return [
            'status' => 201,
            "message" => "Edited successfully."
        ];
    }
    public function add($info){
        $this->create([
            'ip' => $info['ip'],
            'port' => $info['port'],
            'user' => $info['user'],
            'password' => $info['password'],
        ]);
    }
    public function del($id){
        $proxy = $this->where('id', $id)->delete();
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
}
