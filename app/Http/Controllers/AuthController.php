<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailVerification;
use App\Models\PasswordResetToken;
use Validator, Auth, Log, Session, Hash, Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

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
            // Check if email is verified
            if (!Auth::user()->verify_at) {
                Auth::logout();
                return redirect()->back()->withInput()->withErrors(['message' => 'Please verify your email address before logging in.']);
            }
            
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
            'password' => 'required|min:8',
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
            
            // Send email verification
            $this->sendVerificationEmail($user);
            
        } catch(\Exception $errors) {
            Log::error($errors->getMessage());
            return redirect()->back()
            ->withInput()->withErrors(['message' => 'Registration failed: ' . $errors->getMessage()]);
        }

        Session::flash('success', 'Registration successful! Please check your email to verify your account before logging in.');
        return redirect()->route('auth.sign-in');
    }

    public function SignOut()
    {
        Auth::logout();
        return redirect()->route('auth.sign-in');
    }

    public function showForgotPassword()
    {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        return view('pages.auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $email = $request->email;
        $token = Str::random(64);

        // Store password reset token
        PasswordResetToken::updateOrCreate(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => now(),
            ]
        );

        // Send password reset email
        $this->sendPasswordResetEmail($email, $token);

        Session::flash('success', 'Password reset link has been sent to your email address.');
        return redirect()->route('auth.sign-in');
    }

    public function showResetPassword($token)
    {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        $resetToken = PasswordResetToken::where('token', $token)->first();
        
        if (!$resetToken || $resetToken->created_at->addHours(24)->isPast()) {
            Session::flash('error', 'Password reset link is invalid or has expired.');
            return redirect()->route('auth.sign-in');
        }

        return view('pages.auth.reset-password', compact('token'));
    }

    public function resetPassword(Request $request, $token)
    {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $resetToken = PasswordResetToken::where('token', $token)->first();
        
        if (!$resetToken || $resetToken->created_at->addHours(24)->isPast()) {
            Session::flash('error', 'Password reset link is invalid or has expired.');
            return redirect()->route('auth.sign-in');
        }

        // Update user password
        $user = User::where('email', $resetToken->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset token
        $resetToken->delete();

        Session::flash('success', 'Password has been reset successfully. You can now login with your new password.');
        return redirect()->route('auth.sign-in');
    }

    public function verifyEmail($token)
    {
        $verification = EmailVerification::where('token', $token)->first();
        
        if (!$verification || $verification->isExpired()) {
            Session::flash('error', 'Email verification link is invalid or has expired.');
            return redirect()->route('auth.sign-in');
        }

        $user = User::where('email', $verification->email)->first();
        
        if (!$user) {
            Session::flash('error', 'User not found.');
            return redirect()->route('auth.sign-in');
        }

        // Mark email as verified
        $user->verify_at = now();
        $user->save();

        // Delete verification record
        $verification->delete();

        Session::flash('success', 'Email verified successfully! You can now login to your account.');
        return redirect()->route('auth.sign-in');
    }

    public function showResendVerification()
    {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        return view('pages.auth.resend-verification');
    }

    public function resendVerification(Request $request)
    {
        if(Auth::check()) {
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user = User::where('email', $request->email)->first();
        
        if ($user->verify_at) {
            return redirect()->back()->withErrors(['message' => 'Email is already verified.']);
        }

        // Send verification email again
        $this->sendVerificationEmail($user);

        Session::flash('success', 'Verification email has been resent to your email address.');
        return redirect()->route('auth.sign-in');
    }

    public function showProfile()
    {
        if(!Auth::check()) {
            return redirect()->route('auth.sign-in');
        }

        $user = Auth::user();
        return view('pages.auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        if(!Auth::check()) {
            return redirect()->route('auth.sign-in');
        }

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|string|max:50',
            'identity_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $user->update($request->only([
                'name', 'phone', 'birthday', 'gender', 
                'marital_status', 'identity_number', 'address'
            ]));

            Session::flash('success', 'Profile updated successfully!');
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withInput()->withErrors(['message' => 'Failed to update profile.']);
        }

        return redirect()->route('auth.profile');
    }

    public function updatePassword(Request $request)
    {
        if(!Auth::check()) {
            return redirect()->route('auth.sign-in');
        }

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            Session::flash('success', 'Password updated successfully!');
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Failed to update password.']);
        }

        return redirect()->route('auth.profile');
    }

    private function sendVerificationEmail($user)
    {
        $verification = EmailVerification::createForEmail($user->email);
        
        Mail::send('emails.verify-email', [
            'user' => $user,
            'token' => $verification->token
        ], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify Your Email Address');
        });
    }

    private function sendPasswordResetEmail($email, $token)
    {
        Mail::send('emails.reset-password', [
            'email' => $email,
            'token' => $token
        ], function($message) use ($email) {
            $message->to($email);
            $message->subject('Reset Your Password');
        });
    }
}
