<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use DateTime;

class history_model extends Model
{
    use HasFactory;
    protected $fillable = [
        'time',
        'machine',
        'action'
    ];
    protected $table="historys";
    
    public function list($from, $to, $page, $limit){
        $historyList = DB::table("historys")
        ->where([
            ['time', '>', new DateTime($from)],
            ['time', '<', new DateTime($to)]
        ])
        ->offset($page*$limit)->limit($limit)->get();
        return [
            'status' => 201,
            'historylist' => $historyList
        ];
    }
    public function search($from, $to, $page, $limit, $info){
        $cond = collect([]);
        foreach($info as $key => $value){
            $cond->push([$key, 'like', "%".$value."%"]);
        }
        $cond->push(['time', '>', new DateTime($from)]);
        $cond->push(['time', '<', new DateTime($to)]);
        return [
            'status' => 201,
            'historylist' => $this->where(get_object_vars($cond))->offset($page*$limit)->limit($limit)->get()
        ];
    }
    public function get($id){
        $history = $this->where('id', $id)->firstOrFail();
        return [
            'status' => 201,
            'history' => $history
        ];
    }
    public function set($id, $info){
        $history = $this->where('id', $id)->firstOrFail();
        if($info['time']){ $history->time = $info['time']; }
        if($info['machine']){ $history->machine = $info['machine']; }
        if($info['action']){ $history->action = $info['action']; }
        $history->save();
        return [
            'status' => 201,
            "message" => "Edited successfully."
        ];
    }
    public function add($info){
        $this->create([
            'time' => new DateTime($info['time']),
            'machine' => $info['machine'],
            'action' => $info['action'],
        ]);
    }
    public function del($id){
        $history = $this->where('id', $id)->delete();
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
    public function clear($from, $to){
        $historyList = DB::table("historys")
        ->where([
            ['time', '>', new DateTime($from)],
            ['time', '<', new DateTime($to)]
        ])->delete();
        return [
            'status' => 201,
            'message' => 'Resource cleared.',
        ];
    }
}
