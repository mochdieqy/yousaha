<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator, Auth, Log, Session, Hash;

class AuthController extends Controller
{
    public function SignIn() {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        return view('pages.login.index');
    }

    public function SignInProcess(Request $request)
    {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            $validator->errors()->add('message', 'Gagal login');
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $login = Auth::attempt([
            'username' => $request->get('username'),
            'password' => $request->get('password'),
        ], 1);

        if($login) {
            return redirect()->route('home');
        } else {
            return redirect()->back()->withInput()->withErrors(['message' => 'Gagal login']);
        }
    }

    public function SignUp() {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        return view('pages.register.index');
    }

    public function SignUpProcess(Request $request)
    {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|max:12|unique:users,username',
            'password' => 'required',
            'confirmation_password' => 'required|same:password',
            'terms' => 'required',
	        'g-recaptcha-response' => 'required|captcha'
        ]);

        if($validator->fails())
        {
            $validator->errors()->add('message', 'Gagal register');
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user = new User;
        $user->username = str_replace(' ', '', strip_tags($request->username));
        $user->password = Hash::make($request->password);

        try{
            $user->save();
        } catch(\Exception $errors) {
            Log::error($errors->getMessage());
            return redirect()->back()
            ->withInput()->withErrors(['message' => 'Gagal register']);
        }

        Session::flash('message', 'Berhasil register! Silakan melakukan login');
        return redirect()->route('auth.sign-in');
    }

    public function SignOut()
    {
        Auth::logout();
        return redirect()->route('auth.sign-in');
    }
}
