<?php


namespace App\Http\Controllers\Group;


use App\Http\Controllers\Controller;
use App\Http\Controllers\HTTPHelper;
use Illuminate\Http\Request;
use Log;
use Auth;

class ListGroupsController extends Controller
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
        $endpoint = config('services.pronto.url')."/scim/v2/Groups?startIndex={$start}&perPage={$perPage}";

        $result = HTTPHelper::get($endpoint, $headers);
        if (array_key_exists('error', $result)) {
            return view('error', [ 'error' => $result['error']]);
        }

        $bodyContents = $result['contents'];
        $groups = [];
        foreach($bodyContents['Resources'] as $group) {
            $g = [
                'group_name' => $group['displayName'],
                'id' => $group['id'],
                'members' => $group['members'],
            ];
            $groups[] = $g;
        }
        return view('oauth.groups.list', [
            'total' => $bodyContents['totalResults'],
            'pagesize' => $bodyContents['itemsPerPage'],
            'start' => $bodyContents['startIndex'],
            'groups' => $groups,
        ]);
    }

}

