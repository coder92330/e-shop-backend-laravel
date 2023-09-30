<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\log_model;

use Carbon\Carbon;

class LogoutController extends APIController
{

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        $log = new log_model();
        $log->Add(
            array(
                'member_id' => $user->name,
                'function_id'=> "Logged out",
                'function_param'=>"",
                'detail_log'=>""
            )
        );

        return $this->responseSuccess('Successfully logged out.');
    }
}
