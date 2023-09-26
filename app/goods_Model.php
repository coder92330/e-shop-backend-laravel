<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class goods_Model extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'nvid',
        'keyword1',
        'keyword2',
        'keyword3',
    ];
    protected $table="goods";
    
    public function list($page, $limit){
        $goods = DB::table("goods")->offset($page*$limit)->limit($limit)->get();
        return [
            'status' => 201,
            'goods' => $goods
        ];
    }
    public function search($info, $page, $limit){
        $cond = collect([]);
        foreach($info as $key => $value){
            $cond.push([$key, 'like', "%".$value."%"]);
        }
        return [
            'status' => 201,
            'goods' => $this->where(get_object_vars($cond))->offset($page*$limit)->limit($limit)->get()
        ];
    }
    public function get($id){
        $good = $this->where('id', $id)->firstOrFail();
        return [
            'status' => 201,
            'good' => $good
        ];
    }
    public function set($id, $info){
        $good = $this->where('id', $id)->firstOrFail();
        if($info['name']){ $good->name = $info['name']; }
        if($info['nvid']){ $good->nvid = $info['nvid']; }
        if($info['keyword1']){ $good->keyword1 = $info['keyword1']; }
        if($info['keyword2']){ $good->keyword2 = $info['keyword2']; }
        if($info['keyword3']){ $good->keyword3 = $info['keyword3']; }
        $good->save();
        return [
            'status' => 201,
            "message" => "Edited successfully."
        ];
    }
    public function add($info){
        $this->create([
            'name' => $info['name'],
            'nvid' => $info['nvid'],
            'keyword1' => $info['keyword1'],
            'keyword2' => $info['keyword2'],
            'keyword3' => $info['keyword3'],
        ]);
    }
    public function del($id){
        $good = $this->where('id', $id)->delete();
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
    public function getGoodsIdByNvid($nvid){
        $good = $this->where('nvid', $nvid)->firstOrFail();
        return [
            'status' => 201,
            'good' => $good
        ];
    }
}
