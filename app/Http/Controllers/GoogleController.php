<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $user = $request->user();
            $social_user = Socialite::driver('google')->user();

            if($user == null) {
                $finduser = User::where('google_id', $social_user->id)->first();
                if($finduser){
                    Auth::login($finduser);
                    return redirect()->intended('dashboard');
                } else{
                    return redirect()->route('login')->withErrors(['google' => 'Credential not found. Please register first.']);
                }
            }

            $finduser = User::where('google_id', $social_user->id)->first();

            if($finduser){
                if($finduser->email == $user->email){
                    Auth::login($finduser);
                    return redirect()->intended('dashboard');
                }
                else {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->withErrors(['email' => 'Email doesn\'t match your Google account.']);
                }
            } else{
                $user->google_id = $social_user->id;
                $user->save();

                Auth::login($user);
                return redirect()->intended('dashboard');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
