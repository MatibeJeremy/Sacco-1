<?php

namespace App\Http\Controllers\Api\V1;

use JWTAuth;
use App\Models\Account;
use App\Http\Resources\AccountResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;


class AccountController extends Controller
{
    /**
     * @var
     */
    protected $user;

    /**
     * Account controller constructor
     */
    public function __construct()
    {
        $this->middleware('role:ADMIN')->except(['show', 'store', 'update']);
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index() {
        // paginate users with 50 per page
        $accounts = AccountResource::collection(Account::all());
        return response()->json([
           'data' => $accounts,
            'message' => 'Successfully loaded accounts.',
            'status' => 'Success'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request -> all(), [
            'name' => 'required|string|max:255',
            'balance' => 'required|float',
        ]);

        //check if request is valid
        if ($validator->fails()){
            return response()->json([
                'error' => [
                    'message' => $validator->messages()->first(),
                    'status' => 'Fail'
                ]
            ], 422);
        }

        // get account creation data
        $account_input = $request->all();

        // create account
        $account = new Account();
        $account->name = $account_input['name'];
        $account->balance = $account_input['balance'];
        $account->save();

        return response()->json([
            'data' => $account,
            'message' => 'Successfully registered user.',
            'status' => 'Success'
        ], 201);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        // get account
        $account = Account::find($id);
        if($account){
            return response()->json([
                'data' => new AccountResource($account),
                'message' => 'Successfully loaded account'
            ], 200);
        }

         // error finding account
        return response()->json([
           'error' => [
               'message' => 'User with id '.$id. ' not found.',
               'status' => 'Fail'
           ]
        ], 403);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        // find account to update
        $account = Account::find($id);

        $account->name = $request->input('name');
        $account->balance = $request->input('balance');
        $account->save();
        return response()->json([
            'data' => new AccountResource($account),
            'message' => 'Successfully updated user.',
            'status' => 'Success'
        ], 200);
    }



}
