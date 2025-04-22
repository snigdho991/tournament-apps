<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{

    public function toResponse($request)
    {
        
        if (Auth::user()->role == "Administrator") {
        	$home = config('fortify.admin');
        } elseif (Auth::user()->role == "Player") {
        	$home = config('fortify.player');
        }

        return $request->wantsJson()
                    ? response()->json(['two_factor' => false])
                    : redirect($home);
    }

}