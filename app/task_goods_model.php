<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class task_goods_model extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_id',
        'goods_id',
    ];
    protected $table="task_goods";
    
    public function list($job_id){
        $task_goods = DB::table("task_goods")
        ->join("task_goods.job_id", "goods.id")
        ->where('job_id', $job_id)
        ->get();
        return [
            'status' => 201,
            'task_goods' => $task_goods
        ];
    }
    public function search($info, $page, $limit){
        $cond = collect([]);
        foreach($info as $key => $value){
            $cond.push([$key, 'like', "%".$value."%"]);
        }
        return [
            'status' => 201,
            'task_goods' => $this->where(get_object_vars($cond))->offset($page*$limit)->limit($limit)->get()
        ];
    }
    public function get($id){
        $task_good = $this->where('id', $id)->firstOrFail();
        return [
            'status' => 201,
            'task_good' => $task_good
        ];
    }
    public function set($id, $info){
        $task_good = $this->where('id', $id)->firstOrFail();
        if($info['job_id']){ $task_good->job_id = $info['job_id']; }
        if($info['goods_id']){ $task_good->goods_id = $info['goods_id']; }
        $task_good->save();
        return [
            'status' => 201,
            "message" => "Edited successfully."
        ];
    }
    public function add($info){
        $this->create([
            'job_id' => $info['job_id'],
            'goods_id' => $info['goods_id'],
        ]);
        return [
            'status' => 201,
            'message' => 'Resource created.',
        ];
    }
    public function del($id){
        $task_good = $this->where('id', $id)->delete();
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
    public function unset($goods_id){
        $task_surfing = $this->where('goods_id', $goods_id)->delete();
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
}
