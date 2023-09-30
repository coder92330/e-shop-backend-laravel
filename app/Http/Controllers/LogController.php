<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\log_model;
use App\function_model;
use App\role_model;

class LogController extends APIController
{
    public function indexOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $member_logModel = new log_model();

        $permission = $this->getPermission("view", $user);
        if($permission) {
            if($request['info']){
                $logList = $member_logModel->search($request['from'], $request['to'], $request['page'], $request['limit'], $request['info']);
            } else {
                $logList = $member_logModel->list($request['from'], $request['to'], $request['page'], $request['limit']);
            }
            
            return [
                "status" => 201,
                "loglist" => $logList
            ];
        } else {
            return [
                "status" => 401,
                "message" => "This is forbidden."
            ];
        }
    }
    public function addOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $validator = Validator::make($request->all(), [
            'member_id' => 'required',
            'function_id' => 'required',
            'function_param' => 'required',
            'detail_log' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("view", $user);
            if($permission) {
                $member_logModel = new log_model();
                $log = $member_logModel->add($request->all());
                return $this->responseSuccess('Add successfully.');
            } else {
                return [
                    "status" => 401,
                    "message" => "This is forbidden."
                ];
            }
        } catch (Exception $e) {
            return $this->responseServerError('Registration error.');
        }
    }
    public function clear(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        try {
            $permission = $this->getPermission("view", $user);
            if($permission) {
                $member_logModel = new log_model();
                $log = $member_logModel->clear();
                return response()->json($log);
            } else {
                return [
                    "status" => 401,
                    "message" => "This is forbidden."
                ];
            }
        } catch (Exception $e) {
            return $this->responseServerError('Delete error.');
        }
    } 
    public function getPermission($funcType, $user){
        $functionModel = new function_model();
        $roleModel = new role_model();
        $func = $functionModel->getIdByName("log", $funcType);
        $role = $roleModel->getLevelPermission($user->level_id, $func['id']);
        return $role['permission'];
    }
}
