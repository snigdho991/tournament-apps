<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

use App\Mail\NewPlayerReg;
use Mail;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'  => ['required', 'regex:/^9\d{7}$/'],
            'gender' => ['required', 'string', 'max:255'],
            'age'    => ['required'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        $user = new User();
        $user->name         = $input['name'];
        $user->email        = $input['email'];
        $user->phone        = '+357'.$input['phone'];
        $user->gender       = $input['gender'];
        $user->age          = $input['age'];
        $user->password     = Hash::make($input['password']);
        $user->role         = 'Player';

        $user->save();

        $user->assignRole('Player');

        $admin = User::where('role', 'Administrator')->first();
        Mail::to($admin->email)->send(new NewPlayerReg($user));

        return $user;
    }
}
