<?php

namespace App\Http\Controllers;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function fbPage() {
        return Socialite::driver('facebook')->redirect();
    }

    public function fbPageCallback() {
        try {
            $user = Socialite::driver('facebook')->user();
            dump($user);
        } catch (\Exception $e){

        }
    }
}
