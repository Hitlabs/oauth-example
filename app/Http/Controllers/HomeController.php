<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Str;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $state = Str::random(12);
        $user = Auth::user();
        $configured = false;
        $viewProperties = [];
        $viewProperties['user'] = $user;
        if (!empty(config('services.pronto.clientid'))
            && !empty(config('services.pronto.clientsecret'))
            && !empty(config('app.url'))) {
            $configured = true;
            $clientId = config('services.pronto.clientid');
            $redirectUri = config('app.url').'/oauth/auth';
            session()->put('oauth_state', $state);
            session()->put('user_id', $user->id);
            $scopes = 'user-view user-create user-update user-delete group-view group-create group-update group-delete';
            $params = rawurldecode("client_id={$clientId}&".
                                   "grant_type=client_credentials&".
                                   "response_type=code&".
                                   "state={$state}&".
                                   "redirect_uri={$redirectUri}&".
                                   "scope={$scopes}");
            $url =  config('services.pronto.url')."/oauth/authorize?{$params}";
            $connected = $user->oauth_token !== null;
            $expired = Carbon::now()->isAfter($user->oauth_expires);
            $viewProperties['url'] = $url;
            $viewProperties['oauth_connected'] = $connected;
            $viewProperties['oauth_expired'] = $expired;
        }
        $viewProperties['configured'] = $configured;
        return view('home', $viewProperties);
    }
}
