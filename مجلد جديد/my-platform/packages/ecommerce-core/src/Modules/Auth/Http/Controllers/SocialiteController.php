<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
// use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(string $provider)
    {
        // return Socialite::driver($provider)->redirect();
        return response()->json(['message' => "Redirect to $provider"]);
    }

    public function callback(string $provider)
    {
        // $user = Socialite::driver($provider)->user();
        // Login or Register logic
        return response()->json(['message' => "Callback from $provider"]);
    }
}
