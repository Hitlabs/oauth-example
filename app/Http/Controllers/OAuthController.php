<?php


namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Auth;

class OAuthController
{

    public function error(Request $request)
    {
        return view('oauth/error', [
            'error' => $request->error
        ]);
    }

    public function success(Request $request)
    {
        $json = json_decode($request->result);
        $accessToken = $json->access_token;
        $refreshToken = $json->refresh_token;
        $expiry = Carbon::now()->addSeconds($json->expires_in)->toDateString();
        return view('oauth/success', [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expiry' => $expiry,
        ]);
    }

    public function callback(Request $request)
    {
        $requestUrl = $request->fullUrl();
        Log::info("OAuth callback request with url `{$requestUrl}`...");
        if ($request->has('error')) {
            $error = $request->get('error');
            $description = rawurldecode($request->get('error_description', 'No description provided.'));
            $hint = rawurldecode($request->get('hint', 'No hint provided.'));
            $message = "Unable to complete OAuth. Failed with error `{$error}` and message `{$description}, {$hint}`";
            Log::error($message);
            return redirect()->route('oauth.error', ['error' => $message]);
        }

        //If no error was returned, proceed... pull state from session to match with the request.
        if (!session()->has('oauth_state')) {
            Log::info('No `oauth_state` in current session!');
            return redirect()->route('oauth.error', ['error' => 'OAuth state not in session.']);
        }
        $sessionState = session()->get('oauth_state');

        //Request must provide a state variable to match with the session
        if (!$request->has('state')) {
            Log::info('No `state` provided in the request!');
            return redirect()->route('oauth.error', ['error' => 'OAuth state not in request.']);
        }
        $requestState = $request->get('state');

        //Identify user from session data
        $userId = session()->get('user_id');
        $user = User::find($userId);
        if ($user === null) {
            return redirect()->route('oauth.error', ['error' => 'Unable to identify requesting user from session data.']);
        }
        if ($request->has('code')) {
            Log::info('Recieved OAuth code. Proceeding with OAuth.');
            if ($sessionState === $requestState) {
                $requestOptions = [
                    'grant_type' => 'authorization_code',
                    'client_id' => config('services.pronto.clientid'),
                    'client_secret' => config('services.pronto.clientsecret'),
                    'redirect_uri' => config('app.url').'/oauth/auth',
                    'code' => $request->code
                ];
                // Call Pronto to complete OAuth token exchange
                $completeEndpoint = config('services.pronto.url').'/oauth/token';
                $result = HTTPHelper::post($completeEndpoint, [], $requestOptions);
                if (array_key_exists('error', $result)) {
                    return redirect()->route('oauth.error', ['error' => $result['error']]);
                }

                $result = $result['contents'];
                Log::error("Completed OAuth process. Saving OAuth Token data to user `{$userId}`.");
                $user->oauth_token = $result['access_token'];
                $user->oauth_refreshtoken = $result['refresh_token'];
                $user->oauth_expires = Carbon::now()->addSeconds($result['expires_in']);
                $user->save();
                return redirect()->route('oauth.success', ['result' => json_encode($result)]);
            }
            Log::error("State in OAuth request did not match stored session state! Session State: `{$sessionState}`, Request State: `{$requestState}`");
            return redirect()->route('oauth.error', ['error' => 'Mismatched OAuth State.']);
        }
        return redirect()->route('oauth.error', ['error' => 'Oauth `code` not present in request.']);
    }

    public function refresh(Request $request)
    {
        $user = Auth::user();
        Log::info("User `{$user->id}` is attempting to refresh their OAuth token...");
        if ($user === null) {
            return redirect()->route('oauth.error', ['error' => 'Unable to refresh OAuth token. User not found.']);
        }
        if ($user->oauth_refreshtoken === null) {
            return redirect()->route('oauth.error', ['error' => 'Unable to refresh OAuth token. User does not have a token.']);
        }
        $requestOptions = [
            'grant_type' => 'refresh_token',
            'client_id' => config('services.pronto.clientid'),
            'client_secret' => config('services.pronto.clientsecret'),
            'redirect_uri' => config('app.url').'/oauth/auth',
            'refresh_token' => $user->oauth_refreshtoken,
        ];
        // Call Pronto to refresh token
        $endpoint = config('services.pronto.url').'/oauth/token';
        Log::info("Attempting to refresh OAuth Token. Executing POST to `{$endpoint}`...");
        $result = HTTPHelper::post($endpoint, [], $requestOptions);
        if (array_key_exists('error', $result)) {
            return redirect()->route('oauth.error', ['error' => $result['error']]);
        }

        $bodyContents = $result['contents'];
        Log::error("Completed OAuth refresh request. Saving OAuth Token data to user `{$user->id}`.");
        $user->oauth_token = $bodyContents['access_token'];
        $user->oauth_refreshtoken = $bodyContents['refresh_token'];
        $user->oauth_expires = Carbon::now()->addSeconds($bodyContents['expires_in']);
        $user->save();
        return redirect()->route('oauth.success', ['result' => json_encode($bodyContents)]);
    }

}
