<?php

namespace App\Http\Controllers\Api\V1;


require '../vendor/autoload.php';

use App\Http\Resources\UserResource;
use App\Mail\VerifyEmail;
use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use App\Models\VerifyUser;
use Dingo\Api\Http\Response;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use AfricasTalking\SDK\AfricasTalking;

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
        // Set your sms app credentials
        $username   = "sandbox";
        $apiKey     = "c2ce24cfae870831362bc20f322b1ef5541df2117109aff90634b1567dba19dd";

        // Initialize the SDK
        $AT         = new AfricasTalking($username, $apiKey);

        // Get the SMS service
        $sms        = $AT->sms();

        // Set the numbers you want to send to in international format
        $recipients = "+254701176746";

        // Set your message
        $message    = "You have successfully been registered onto RE/SYST";

        // Set your shortCode or senderId
        $from       = "RE/SYST";


        // get supported roles
        $roles = Role::all();

        // create validator for user object
        $validator = Validator::make($request -> all(), [
            'name' => 'required|string|max: 255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:'.$roles->implode('name',',')
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

        //Verify user
        $verifyUser = VerifyUser::create([
            'user_id' => $user->id,
            'token' => sha1(time())
        ]);

        //send email
        Mail::to($user->email)->send(new VerifyEmail($user));

        //send sms
        try {
            // Thats it, hit send and we'll take care of the rest
            $result = $sms->send([
                'to'      => $recipients,
                'message' => $message,
                'from'    => $from
            ]);

            print_r($result);
        } catch (Exception $e) {
            echo "Error: ".$e->getMessage();
        }

//         $token = JWTAuth::fromUser($user);


        return response()->json([
           'data' => new UserResource($user),
           'message' => 'Successfully registered user.',
           'status' => 'Success'
        ], 201);

    }

    /**
     *  Verifies user using the token in the VerifyUser table
     * @param $token
     * @return
     */

    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if(isset($verifyUser) ){
            $user = $verifyUser->user;
            if(!$user->verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                $status = "Your e-mail is verified. You can now login.";
            } else {
                $status = "Your e-mail is already verified. You can now login.";
            }
        } else {
            return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
        }
        return redirect('/login')->with('status', $status);
    }

    public function authenticated(Request $request, $user)
    {
        if (!$user->verified) {
            auth()->logout();
            return back()->with('warning', 'You need to confirm your account. We have sent you an activation code, please check your email.');
        }
        return redirect()->intended($this->redirectPath());
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
            return $this->respondWithToken($token, auth()->user());
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


    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = Auth::user();

        return response()->json([
            'data'=> new UserResource($user),
            'message'=>'Loaded user successfully.',
            'status'=>'Fail'
        ], 200);
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
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => new UserResource($user),
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
        return Auth::guard('');
    }

}
