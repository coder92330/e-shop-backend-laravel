<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Validator;

use App\machine_model;
use App\function_model;
use App\role_model;

class MachineController extends APIController
{
    public function indexOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $machineModel = new machine_model();

        $permission = $this->getPermission("view", $user);
        if($permission) {
            $machinesList = $machineModel->all();
            return [
                "status" => 201,
                "result" => $machinesList
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
            'type' => 'required|string',
            'machine_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("add", $user);
            if($permission) {
                $machineModel = new machine_model();
                $machine = $machineModel->add($request->all());
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
            'ip' => 'required|string|unique:machines',
            'type' => 'required|string',
            'machine_id' => 'required|string|unique',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("edit", $user);
            if($permission) {
                $machineModel = new machine_model();
                $machine = $machineModel->set($request['id'], $request->all());
                return response()->json($machine);
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
                $machineModel = new machine_model();
                $machine = $machineModel->del($request['id']);
                return response()->json($machine);
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
    public function getMachineById(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $machineModel = new machine_model();
        $permission = $this->getPermission("view", $user);
        if($permission) {
            return [
                'status' => 201,
                'machine' => $machineModel->get($request['id'])
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
        $func = $functionModel->getIdByName("machine", $funcType);
        $role = $roleModel->getLevelPermission($user->level_id, $func['id']);
        return $role['permission'];
    }
}
