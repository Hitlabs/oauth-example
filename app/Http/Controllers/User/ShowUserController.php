<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use App\Http\Controllers\HTTPHelper;
use Illuminate\Http\Request;
use Log;
use Auth;

class ShowUserController extends Controller
{

    private const ROLES = ['user', 'admin', 'manager'];

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function show(Request $request, $userId, $mode)
    {
        $user = Auth::user();
        $endpoint = config('services.pronto.url')."/scim/v2/Users/{$userId}";
        $headers = ['Authorization' => "Bearer {$user->oauth_token}"];
        $result = HTTPHelper::get($endpoint, $headers);
        if (array_key_exists('error', $result)) {
            return view('error', [ 'error' => $result['error']]);
        }

        $bodyContents = $result['contents'];
        Log::info($bodyContents);
        $user = [
            'firstname' => $bodyContents['name']['givenName'],
            'lastname' => $bodyContents['name']['familyName'],
            'email' => $bodyContents['emails'][0]['value'],
            'id' => $bodyContents['id'],
            'username' => $bodyContents['userName'],
            'active' => $bodyContents['active'],
            'role' => $bodyContents['roles'][0],
        ];
        $view = 'oauth.user.show';
        if ($mode === 'update') {
            $view = 'oauth.user.update';
        }
        return view($view, ['user' => $user, 'roles' => self::ROLES]);
    }

}
