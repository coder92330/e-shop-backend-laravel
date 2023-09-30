<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;



class browser_model extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'agent'
    ];
    protected $table="browsers";
    
    public function list($page, $limit){
        $browserList = DB::table("browsers")->offset($page*$limit)->limit($limit)->get();
        return [
            'status' => 201,
            'browserlist' => $browserList
        ];
    }
    public function search($info, $page, $limit){
        $cond = collect([]);
        foreach($info as $key => $value){
            $cond.push([$key, 'like', "%".$value."%"]);
        }
        return [
            'status' => 201,
            'browserlist' => $this->where(get_object_vars($cond))->offset($page*$limit)->limit($limit)->get()
        ];
    }
    public function get($id){
        $browser = $this->where('id', $id)->firstOrFail();
        return [
            'status' => 201,
            'browser' => $browser
        ];
    }
    public function set($id, $info){
        $browser = $this->where('id', $id)->firstOrFail();
        if($info['name']){ $browser->name = $info['name']; }
        if($info['agent']){ $browser->agent = $info['agent']; }
        $browser->save();
        return [
            'status' => 201,
            "message" => "Edited successfully."
        ];
    }
    public function add($info){
        $this->create([
            'name' => $info['name'],
            'agent' => $info['agent'],
        ]);
    }
    public function del($id){
        $browser = $this->where('id', $id)->delete();
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
}
