<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Validator;

use App\goods_model;
use App\function_model;
use App\role_model;
use App\log_model;


class GoodsController extends APIController
{
    public function indexOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $goodsModel = new goods_model();

        $permission = $this->getPermission("view", $user);
        if($permission) {
            $goodsList = $goodsModel->all();

            $log = new log_model();
        $log->Add(
            array(
                'member_id' => $user->id,
                'function_id'=> 5,
                'function_param'=>"",
                'detail_log'=>""
            )
        );
            return [
                "status" => 201,
                "result" => $goodsList
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
        $req_arr=$request->all();
        $validator = Validator::make($req_arr, [
            'name' => 'required|string|max:255',
            'nvid' => 'required|string|unique:goods',
            'keyword1' => 'required|string',
            'keyword2' => 'required|string',
            'keyword3' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("add", $user);
            if($permission) {
                $goodsModel = new goods_model();
                $good = $goodsModel->add($req_arr);

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
                         'function_id'=> 6,
                        'function_param'=>$req_arr['name'].$req_arr['nvid'],
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
        $req_arr=$equest->all();
        $validator = Validator::make($req_arr, [
            'name' => 'required|string|max:255',
            'nvid' => 'required|string',
            'keyword1' => 'required|string',
            'keyword2' => 'required|string',
            'keyword3' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        //TO-DO-make-nvid-unique
        try {
            $permission = $this->getPermission("edit", $user);
            if($permission) {
                $goodsModel = new goods_model();
                $good = $goodsModel->set($request['id'], $req_arr);

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
                         'function_id'=> 7,
                        'function_param'=>$req_arr['name'].$req_arr['nvid'],
                        'detail_log'=>""
                    )
                );

                return response()->json($good);
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
                $goodsModel = new goods_model();
                $good = $goodsModel->del($request['id']);
                $good=$goodsModel->get($request['id']);

                $log = new log_model();
                $log->Add(
                    array(
                        'member_id' => $user->id,
                        'function_id'=> 8,
                        'function_param'=>$good['good']['name'],
                        'detail_log'=>""
                    )
                );

                return response()->json($good);
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
    public function getGoodById(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $goodsModel = new goods_model();
        $permission = $this->getPermission("view", $user);
        if($permission) {
            return [
                'status' => 201,
                'good' => $goodsModel->get($request['id'])
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
        $func = $functionModel->getIdByName("goods", $funcType);
        $role = $roleModel->getLevelPermission($user->level_id, $func['id']);
        return $role['permission'];
    }
}
