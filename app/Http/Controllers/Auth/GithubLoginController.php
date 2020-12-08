<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class GithubLoginController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     */
    public function handleProviderCallback()
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::where('provider_id',$githubUser->getId())->orWhere('email',$githubUser->getEmail())->first();

        if(!$user)
        {
            $user = User::create([
                'name' => $githubUser->getName(),
                'email' => $githubUser->getEmail(),
                'provider_id' => $githubUser->getId()
            ]);
        }

        if(!$user->provider_id)
        {
            $user->update([
                'provider_id' => $githubUser->getId()
            ]);
        }

        auth()->login($user,true);

        return redirect()->route('home');
    }
}
