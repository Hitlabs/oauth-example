<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use App\Http\Controllers\HTTPHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Log;

class UpdateUserController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'user_id' => ['required', 'integer'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'role' => ['string', 'required', 'in:user,manager,admin'],
        ]);
    }

    protected function update(Request $request)
    {
        //Request data
        $userId = $request->get('user_id');
        $firstname = $request->get('first_name');
        $lastname = $request->get('last_name');
        $email = $request->get('email');
        $role = $request->get('role');
        //Logged in user info
        $user = Auth::user();
        $endpoint = config('services.pronto.url')."/scim/v2/Users/{$userId}";
        $headers = ['Authorization' => "Bearer {$user->oauth_token}"];
        //Construct request
        $formParameters = [
            'schemas' => ['urn:ietf:params:scim:schemas:core:2.0:User'],
            'userName' => $email,
            'name' => [
                'givenName' => $firstname,
                'familyName' => $lastname,
            ],
            'displayName' => "{$firstname} {$lastname}",
            'emails' => [
                [
                    'primary' => true,
                    'type' => 'work',
                    'value' => $email,
                ]
            ],
            'locale' => 'en-US',
            'active' => true,
            'groups' => [],
            'roles' => [$role]
        ];

        $result = HTTPHelper::put($endpoint, $headers, $formParameters);
        if (array_key_exists('error', $result)) {
            return view('error', [ 'error' => $result['error']]);
        }

        $userId = $result['contents']['id'];
        return redirect("/users/{$userId}/show");
    }

}
