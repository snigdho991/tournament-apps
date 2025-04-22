<?php

namespace App\Http\Controllers\Ums;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Auth;
use Session;
use Image;

class ProfileController extends Controller
{
    public function save_basic_info(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'name'  => 'required',
            'phone' => 'required',
            'email' => 'required|unique:users,email,'.$user->id
        ]);

        $find_user = User::findOrFail($user->id);

        $find_user->name  = $request->name;
        $find_user->email = $request->email;
        $find_user->phone = '+357'.$request->phone;

        if ($request->hasFile('profile_photo_path')) {
        	$image_tmp = $request->file('profile_photo_path');
            if ($image_tmp->isValid()) {
                $image_name = $image_tmp->getClientOriginalName();
                $extension = $image_tmp->getClientOriginalExtension();
                $image_new_name = $image_name.'-'.rand(111,99999).'.'.$extension;

                $original_image_path = 'assets/uploads/users/'.$image_new_name;
                
                Image::make($image_tmp)->save($original_image_path);
                
                $find_user->profile_photo_path = $image_new_name;
            }
        }

        $find_user->save();

        Session::flash('success', 'Basic Information Updated Successfully !');
        return redirect()->back();
    }

    public function change_auth_password(Request $request)
    {
        $this->validate($request, [
            'oldpassword'           => 'required',
            'newpassword'           => 'required|string|min:8',
            'password_confirmation' => 'required|same:newpassword',
        ]);

        $hashedPassword = Auth::user()->password;
 
        if (\Hash::check($request->oldpassword , $hashedPassword )) {
 
            if (!\Hash::check($request->newpassword , $hashedPassword)) {

                $users = User::find(Auth::user()->id);
                $users->password = bcrypt($request->newpassword);

                \Cookie::queue(\Cookie::make('passwordcng', 'Password Changed Successfully ! Login again with the new password.', 0.1));
                
                User::where('id', Auth::user()->id)->update(array('password' => $users->password));
                
                return redirect('/login');
            } else {
                Session::flash('error', 'New password can\'t be same as Old Password. Update Denied !');
                return redirect()->back();
            }

        } else {
            Session::flash('error', 'The old password does not match. Update Denied !');
            return redirect()->back();
        }
    }
}
