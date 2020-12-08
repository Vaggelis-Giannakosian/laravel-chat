<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialiteLoginController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     */
    public function handleProviderCallback($provider)
    {
        $socialiteUser = Socialite::driver($provider)->user();

        $user = User::where('email',$socialiteUser->getEmail())->first();

        if(!$user)
        {
            $user = User::create([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'provider_id' => $socialiteUser->getId(),
                'provider' => $provider
            ]);
        }else{
            $user->update([
                'provider_id' => $socialiteUser->getId(),
                'provider' => $provider
            ]);
        }

        auth()->login($user,true);

        return redirect()->route('home');
    }
}
