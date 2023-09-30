<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use DateTime;

use App\member_model;
use App\level_model;
use App\log_model;

use Carbon\Carbon;

class LoginController extends ApiController
{


    public function indexOp(Request $request){
        if (! $user = auth()->setRequest($request)->user()) {
            return $this->responseUnauthorized();
        }
        return [
            'status' => 201,
            'user' => member_model::where('id', $user->id)->firstOrFail()
        ];
    }
    public function loginOp()
    {
        $credentials = request(['name', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->responseUnauthorized();
        }
        // Get the user data.
        $user = auth()->user();
        $levelModel = new level_model();

        $today = new DateTime();
        $memberModel = new member_model();
        $me = $memberModel->where('id', $user->id)->firstOrFail();
        $me->last_login = $today->format("Y-m-d H:i:s");
        $me->save();


        $log = new log_model();
        $log->Add(
            array(
                'member_id' => $user->id,
                'function_id'=> 100,
                'function_param'=>"Logged in",
                'detail_log'=>""
            )
        );
        return response()->json([
            'status' => 200,
            'message' => 'Authorized.',
            'access_token' => 'bearer '.$token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => array(
                'id' => $user->id,
                'name' => $user->name,
                'level_id' => $user->level_id,
                'role' => $levelModel->getLevelById($user->level_id)
            )
        ], 200);
    }
}
