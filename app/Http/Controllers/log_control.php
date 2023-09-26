<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class log_control extends Controller
{
    // $info = [ $info ]
    public function Add( $info ) {
        echo($info);
    }
    public function List($from,$to,$page,$limit){

    }
    public function Get($id){

    }
    public function Search ($key,$page,$limit){

    }
    public function Clear($from,$to){

    }
}
