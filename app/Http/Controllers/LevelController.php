<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Validator;

use App\level_model;
use App\function_model;
use App\role_model;

class LevelController extends APIController
{
    public function indexOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        if($user->level_id === 1){
            $levelModel = new level_model();
            return response()->json([
                'status' => 201,
                'levels' => $levelModel->getLevelList()
            ]);
        } else {
            return response()->json([
                'status' => 201,
                'levels' => []
            ]);
        }
    }

    public function addOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        if($user->level_id === 1){
            $levelModel = new level_model();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseUnprocessable($validator->errors());
            }

            // Warning: Data isn't being fully sanitized yet.
            try {
                $result = $levelModel->add($request['id'], $request['name']);
                return response()->json($result);
            } catch (Exception $e) {
                return $this->responseServerError('Error creating resource.');
            }
        } else {
            return $this->responseUnprocessable("Forbidden.");
        }
    }

    public function editOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        if($user->level_id === 1){
            $levelModel = new level_model();
            $roleModel = new role_model();
            try {
                if(request('name')){
                    $result = $levelModel->setName(request('id'), request('name'));
                }
                if(request('permission_list')){
                    $result = $roleModel->setLevelPermissionList(request('id'), request('permission_list'));
                }
                return response()->json($result);
            } catch (Exception $e) {
                return $this->responseServerError('Error editing resource.');
            }
        } else {
            return $this->responseUnprocessable("Forbidden.");
        }
    }

    public function deleteOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        if($user->level_id === 1){
            $levelModel = new level_model();
            try {
                $result = $levelModel->del($request['id']);
                return response()->json($result);
            } catch (Exception $e) {
                return $this->responseServerError('Error deleting resource.');
            }
        } else {
            return $this->responseUnprocessable("Forbidden.");
        }
    }

    public function chkSubmit(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        $data = $request->all();
        if($data['id'] < 1 || $data['name'] === ""){
            return false;
        }
        return true;
    }

    public function getLevelById(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        if($user->level_id === 1){
            $levelModel = new level_model();
            return [
                'level' => $levelModel->getLevelById($request['id']),
                'parentLevel' => $levelModel->getParentLevelById($request['id'])
            ];
        } else {
            return $this->responseUnprocessable("Forbidden.");
        }
    }

    public function temp(Request $request){
        // $functionModel = new function_model();
        // $result = $functionModel->add();
        // $roleModel = new role_model();
        // $result = $roleModel->add();
        // $roleModel = new role_model();
        // $result = $roleModel->setLevelPermissionList($request['level_id'], $request['permission_list']);
        return response()->json($result);
    }
}
