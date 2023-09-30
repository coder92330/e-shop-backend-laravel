<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\APIController;

use App\browser_model;
use App\function_model;
use App\role_model;
use App\log_model;

class BrowserController extends APIController
{
    public function indexOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }

        
        $browserModel = new browser_model();

        $permission = $this->getPermission("view", $user);
        if($permission) {
            $browserList = $browserModel->all();

            $log = new log_model();
            $log->Add(
            array(
                'member_id' => $user->id,
                'function_id'=> 17,
                'function_param'=> "Viewed Browser page",
                'detail_log'=>""
            )
        );

            return [
                "status" => 201,
                "result" => $browserList
            ];
        } else {
            return [
                "status" => 401,
                "message" => "This is forbidden."
            ];
        }
    }
    public function addOp(Request $request){
        $req_arr = $request->all();
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $validator = Validator::make($req_arr, [
            'name' => 'required|string',
            'agent' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("add", $user);
            if($permission) {
                $browserModel = new browser_model();
                $browser = $browserModel->add($req_arr);

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
                        'function_id'=> 17,
                        'function_param'=>implode(" ",$req_arr),
                        'detail_log'=>""
                    )
                );

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
    public function editOp(Request $request){

        $req_arr=$request->all();
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $validator = Validator::make($req_arr, [
            'name' => 'required|string',
            'agent' => 'required|string'
        ]);

        

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("edit", $user);
            if($permission) {
                $browserModel = new browser_model();
                $browser = $browserModel->set($request['id'], $req_arr);
                // $logModel = new log_model();
                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
                        'function_id'=> 19,
                        'function_param'=>implode(" ",$req_arr),
                        'detail_log'=>""
                    )
                );
                return response()->json($browser);
            } else {
                return [
                    "status" => 401,
                    "message" => "This is forbidden."
                ];
            }
        } catch (Exception $e) {
            return $this->responseServerError('Edit error.');
        }
    }
    public function deleteOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        try {
            $permission = $this->getPermission("del", $user);
            if($permission) {
                $browserModel = new browser_model();
                $browser = $browserModel->del($request['id']);

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
                        'function_id'=> 20,
                        'function_param'=>implode(" ",$request->all()),
                        'detail_log'=>""
                    )
                );
                return response()->json($browser);
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
    public function getBrowserById(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $browserModel = new browser_model();
        $permission = $this->getPermission("view", $user);
        if($permission) {
            return [
                'status' => 201,
                'browser' => $browserModel->get($request['id'])
            ];
        } else {
            return [
                "status" => 401,
                "message" => "This is forbidden."
            ];
        }
    }
    public function getPermission($funcType, $user){
        $functionModel = new function_model();
        $roleModel = new role_model();
        $func = $functionModel->getIdByName("browser", $funcType);
        $role = $roleModel->getLevelPermission($user->level_id, $func['id']);
        return $role['permission'];
    }
}
