<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Session;

class ThemeController extends Controller
{
    public function select_theme(Request $request)
    {
        $theme_value = $request->get('theme');

        $user = Auth::user();
        $user->theme = $theme_value;
        $user->save();

        Session::flash('success', 'Theme Selected Successfully !');
        return redirect()->back();
    }
}
