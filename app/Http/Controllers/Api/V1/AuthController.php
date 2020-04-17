<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\UserResource;
use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        // exempt user registration from having to login
        $this->middleware('auth:api')->except(['register', 'login']);
    }

    /**
     * Returns user if successfully created
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request) {
        // get supported roles
        $roles = Role::all();

        // create validator for user object
        $validator = Validator::make($request -> all(), [
            'name' => 'required|string|max: 255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:'.$roles->implode('name', ', ')
        ]);

        // check if validator fails
        if ($validator -> fails()) {
            return response()->json([
                'error' => [
                    'message' => $validator->messages()->first(),
                    'status' => 'Fail'
                ]
            ], 422);
        }

        // get registration data
        $user_input = $request->all();

        // create new user
        $user = new User();
        $user->name =$user_input['name'];
        $user->email = $user_input['email'];
        $user->password = bcrypt($user_input['password']);
        $user->save();

        // find role
        $role = Role::where('name', $user_input['role'])->first();

        // set user role
        $user->setUserRole($role->id, $user->id);

        // create a corresponding account
        $account = new Account();
        $account->name = str_random(10);
        $account->balance = 0.00;
        $user->account()->save($account);


        return response()->json([
           'data' => new UserResource($user),
           'message' => 'Successfully registered user.',
           'status' => 'Success'
        ], 201);

    }

    /**
     * Logs user in and provides an access token
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'message' => $validator->messages()->first(),
                    'status' => 'Fail'
                ]
            ], 401);
        }

        // get authentication token
        $credentials = $request->only('email', 'password');
        $token = $this->guard()->attempt($credentials);

        // login successfully
        if($token){
            return $this->respondWithToken($token);
        }

        // invalid credentials response
        return response()->json([
            'error' => [
                'message' => 'Login failed. Invalid email or password',
                'status' => 'Fail'
            ]
        ], 401);
    }

    /**
     * Logs user out
     * @return JsonResponse
     */
    public function logout(){
        // logout user and blacklist token forever
        $this->guard()->logout();

        return response()->json([
            'message' => 'Successfully logged out.',
            'status' => 'Success'
        ]);
    }


    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ], 200);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

}
