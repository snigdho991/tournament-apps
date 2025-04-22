<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function generate_role()
    {
    	$role_one   = Role::create(['name' => 'Administrator']);
    	$role_two   = Role::create(['name' => 'Player']);

    	return true;
    }
}
