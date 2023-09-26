<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class surfing_model extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'url'
    ];
    protected $table="surfings";
    
    public function list($page, $limit){
        $surfingList = DB::table("surfings")->offset($page*$limit)->limit($limit)->get();
        return [
            'status' => 201,
            'surfinglist' => $surfingList
        ];
    }
    public function search($info, $page, $limit){
        $cond = collect([]);
        foreach($info as $key => $value){
            $cond.push([$key, 'like', "%".$value."%"]);
        }
        return [
            'status' => 201,
            'surfinglist' => $this->where(get_object_vars($cond))->offset($page*$limit)->limit($limit)->get()
        ];
    }
    public function get($id){
        $surfing = $this->where('id', $id)->firstOrFail();
        return [
            'status' => 201,
            'surfing' => $surfing
        ];
    }
    public function set($id, $info){
        $surfing = $this->where('id', $id)->firstOrFail();
        if($info['name']){ $surfing->name = $info['name']; }
        if($info['url']){ $surfing->url = $info['url']; }
        $surfing->save();
        return [
            'status' => 201,
            "message" => "Edited successfully."
        ];
    }
    public function add($info){
        $this->create([
            'name' => $info['name'],
            'url' => $info['url'],
        ]);
    }
    public function del($id){
        $surfing = $this->where('id', $id)->delete();
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
}
