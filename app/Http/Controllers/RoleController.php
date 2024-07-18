<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    //
    public function index(){
        $list = Role::all();
        return response()->json($list);
    }
}
