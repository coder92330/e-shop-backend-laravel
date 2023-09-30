<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Validator;

use App\proxy_model;
use App\function_model;
use App\role_model;
use App\log_model;

class ProxyController extends APIController
{
    public function indexOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $proxyModel = new proxy_model();

        $permission = $this->getPermission("view", $user);
        if($permission) {
            $proxysList = $proxyModel->all();

            $log = new log_model();
            $log->Add(
                array(
                    'member_id' => $user->name,
                        'function_id'=> "Viewed Proxy",
                    'function_param'=>"",
                    'detail_log'=>""
                )
            );

            return [
                "status" => 201,
                "result" => $proxysList
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
            'ip' => 'required|string',
            'port' => 'required|string',
            'user' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("add", $user);
            if($permission) {
                $proxyModel = new proxy_model();
                $proxy = $proxyModel->add($request->all());

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
'function_id'=> "Added Proxy",
                        'function_param'=>request('ip'),
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
            'ip' => 'required|string',
            'port' => 'required|string',
            'user' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("edit", $user);
            if($permission) {
                $proxyModel = new proxy_model();
                $proxy = $proxyModel->set($request['id'], $request->all());

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
'function_id'=> "Edited Proxy",
                        'function_param'=>request("ip"),
                        'detail_log'=>""
                    )
                );

                return response()->json($proxy);
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
                $proxyModel = new proxy_model();
                $proxy = $proxyModel->del($request['id']);
                
                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
'function_id'=> "Deleted Proxy",
                        'function_param'=>$proxyModel->ip,
                        'detail_log'=>""
                    )
                );
                
                return response()->json($proxy);
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
    public function getProxyById(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $proxyModel = new proxy_model();
        $permission = $this->getPermission("view", $user);
        if($permission) {
            return [
                'status' => 201,
                'proxy' => $proxyModel->get($request['id'])
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
        $func = $functionModel->getIdByName("proxy", $funcType);
        $role = $roleModel->getLevelPermission($user->level_id, $func['id']);
        return $role['permission'];
    }
}
