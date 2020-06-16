<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use App\Http\Controllers\HTTPHelper;
use Illuminate\Http\Request;
use Log;
use Auth;


class ListUsersController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function list(Request $request)
    {
        $start = (int) $request->query('start', 1);
        $perPage = (int) $request->query('perpage', 100);
        $user = Auth::user();
        $headers = ['Authorization' => "Bearer {$user->oauth_token}"];
        $endpoint = config('services.pronto.url')."/scim/v2/Users?startIndex={$start}&perPage={$perPage}";

        $result = HTTPHelper::get($endpoint, $headers);
        if (array_key_exists('error', $result)) {
            return view('error', [ 'error' => $result['error']]);
        }

        $bodyContents = $result['contents'];
        $users = [];
        foreach($bodyContents['Resources'] as $record) {
            $user = [
                'firstname' => $record['name']['givenName'],
                'lastname' => $record['name']['familyName'],
                'email' => $record['emails'][0]['value'],
                'id' => $record['id'],
                'roles' => implode(', ', $record['roles']),
            ];
            $users[] = $user;
        }
        return view('oauth.user.list', [
            'total' => $bodyContents['totalResults'],
            'pagesize' => $bodyContents['itemsPerPage'],
            'start' => $bodyContents['startIndex'],
            'users' => $users,
        ]);
    }

}
