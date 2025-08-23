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
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            $validator->errors()->add('message', 'Login failed');
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $login = Auth::attempt([
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ], 1);

        if($login) {
            return redirect()->route('home');
        } else {
            return redirect()->back()->withInput()->withErrors(['message' => 'Login failed']);
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirmation_password' => 'required|same:password',
            'terms' => 'required',
	        'g-recaptcha-response' => 'required|captcha'
        ]);

        if($validator->fails())
        {
            $validator->errors()->add('message', 'Registration failed');
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user = new User;
        $user->name = trim($request->name);
        $user->email = strtolower(trim($request->email));
        $user->password = Hash::make($request->password);

        try{
            $user->save();
        } catch(\Exception $errors) {
            Log::error($errors->getMessage());
            return redirect()->back()
            ->withInput()->withErrors(['message' => 'Registration failed']);
        }

        Session::flash('message', 'Registration successful! Please login');
        return redirect()->route('auth.sign-in');
    }

    public function SignOut()
    {
        Auth::logout();
        return redirect()->route('auth.sign-in');
    }
}
