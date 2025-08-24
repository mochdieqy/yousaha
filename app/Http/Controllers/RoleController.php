<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::all();
        
        return view('pages.employee-roles.roles.index', compact('roles'));
    }
}
