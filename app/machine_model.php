<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class machine_model extends Model
{
    use HasFactory;
    protected $fillable = [
        'ip',
        'type',
        'machine_id',
    ];
    protected $table="machines";
    
    public function list($page, $limit){
        $machines = DB::table("machines")->offset($page*$limit)->limit($limit)->get();
        return [
            'status' => 201,
            'machines' => $machines
        ];
    }
    public function listOfNull(){
        $machines = DB::table("machines")->where("job_id", null)->get();
        return [
            'status' => 201,
            'machines' => $machines
        ];
    }
    public function search($info, $page, $limit){
        $cond = collect([]);
        foreach($info as $key => $value){
            $cond.push([$key, 'like', "%".$value."%"]);
        }
        return [
            'status' => 201,
            'machines' => $this->where(get_object_vars($cond))->offset($page*$limit)->limit($limit)->get()
        ];
    }
    public function get($id){
        $machine = $this->where('id', $id)->firstOrFail();
        return [
            'status' => 201,
            'machine' => $machine
        ];
    }
    public function set($id, $info){
        $machine = $this->where('id', $id)->firstOrFail();
        if($info['ip']){ $machine->ip = $info['ip']; }
        if($info['type']){ $machine->type = $info['type']; }
        if($info['machine_id']){ $machine->machine_id = $info['machine_id']; }
        $machine->save();
        return [
            'status' => 201,
            "message" => "Edited successfully."
        ];
    }
    public function add($info){
        $this->create([
            'ip' => $info['ip'],
            'type' => $info['type'],
            'machine_id' => $info['machine_id'],
        ]);
    }
    public function del($id){
        $machine = $this->where('id', $id)->delete();
        $jobModel = new job_model();
        $jobModel->unsetMachine($id);
        return [
            'status' => 201,
            'message' => 'Resource deleted.',
        ];
    }
}
