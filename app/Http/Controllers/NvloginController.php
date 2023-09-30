<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Validator;

use App\nvlogin_model;
use App\function_model;
use App\role_model;
use App\log_model;

class NvloginController extends APIController
{
    public function indexOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $nvloginModel = new nvlogin_model();

        $permission = $this->getPermission("view", $user);
        if($permission) {
            $nvloginList = $nvloginModel->all();

            $log = new log_model();
            $log->Add(
                array(
                    'member_id' => $user->name,
                        'function_id'=> "Viewed login",
                    'function_param'=>"",
                    'detail_log'=>""
                )
            );

            return [
                "status" => 201,
                "result" => $nvloginList
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
            'user' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("add", $user);
            if($permission) {
                $nvloginModel = new nvlogin_model();
                $nvlogin = $nvloginModel->add($request->all());

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
'function_id'=> "Added login",
                        'function_param'=>request('user'),
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
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $validator = Validator::make($request->all(), [
            'user' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("edit", $user);
            if($permission) {
                $nvloginModel = new nvlogin_model();
                $nvlogin = $nvloginModel->set($request['id'], $request->all());

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
'function_id'=> "Edited login",
                        'function_param'=>request('user'),
                        'detail_log'=>""
                    )
                );

                return response()->json($nvlogin);
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
                $nvloginModel = new nvlogin_model();
                $nvlogin = $nvloginModel->del($request['id']);

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
'function_id'=> "Deleted login",
                        'function_param'=>$nvloginModel->user,
                        'detail_log'=>""
                    )
                );

                return response()->json($nvlogin);
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
    public function getNvloginById(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $nvloginModel = new nvlogin_model();
        $permission = $this->getPermission("view", $user);
        if($permission) {
            return [
                'status' => 201,
                'nvlogin' => $nvloginModel->get($request['id'])
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
        $func = $functionModel->getIdByName("login", $funcType);
        $role = $roleModel->getLevelPermission($user->level_id, $func['id']);
        return $role['permission'];
    }
}
