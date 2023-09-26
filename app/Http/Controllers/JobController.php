<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Validator;

use App\job_model;
use App\function_model;
use App\role_model;
use App\nvlogin_model;
use App\machine_model;

class JobController extends APIController
{
    public function indexOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $jobModel = new job_model();

        $permission = $this->getPermission("view", $user);
        if($permission) {
            $jobList = $jobModel->list();
            return [
                "status" => 201,
                "result" => $jobList
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
            'name' => 'required|string|unique:jobs',
            'login_id' => 'required',
            'machine_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("add", $user);
            if($permission) {
                $jobModel = new job_model();
                $job = $jobModel->add($request->all());
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
            'name' => 'required|string|unique:jobs',
            'login_id' => 'required',
            'machine_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocessable($validator->errors());
        }
        try {
            $permission = $this->getPermission("edit", $user);
            if($permission) {
                $jobModel = new job_model();
                $job = $jobModel->set($request['id'], $request->all());
                return response()->json($job);
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
                $jobMode = new job_model();
                $job = $jobMode->del($request['id']);
                return response()->json($job);
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
    public function getJobById(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $jobModel = new job_model();
        $permission = $this->getPermission("view", $user);
        if($permission) {
            return [
                'status' => 201,
                'job' => $jobModel->get($request['id'])
            ];
        } else {
            return [
                "status" => 401,
                "message" => "This is forbidden."
            ];
        }
    }
    public function getNvloginList(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $nvloginModel = new nvlogin_model();
        return $nvloginModel->listOfNull();
    }
    public function getMachineList(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $machineModel = new machine_model();
        return $machineModel->listOfNull();
    }
    public function getPermission($funcType, $user){
        $functionModel = new function_model();
        $roleModel = new role_model();
        $func = $functionModel->getIdByName("job", $funcType);
        $role = $roleModel->getLevelPermission($user->level_id, $func['id']);
        return $role['permission'];
    }
}
