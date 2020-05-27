<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class TransactionController extends Controller
{
    /**
     * @var
     */
    protected $user;

    /**
     * TransactionController constructor.
     */
    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $transactions = $this->user->account->transactions;
        return response()->json([
            'data' => TransactionResource::collection($transactions),
            'message' => 'Successfully loaded all transactions for user: '.$this->user->name,
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
            'type' => 'required',
            'amount' => 'required',
            'status' => 'required',
            'sender_account_id' => 'required',
            'recipient_account_id' => 'required',
            'sender_user_id' => 'required',
            'recipient_user_id' => 'required',
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

        // get transaction creation data
        $transaction_input = $request->all();

        // create transaction
        $transaction = new Transaction();
        $transaction->uuid =  Str::uuid()->toString();
        $transaction->amount = $transaction_input['amount'];
        $transaction->type = $transaction_input['type'];
        $transaction->status = $transaction_input['status'];
        $transaction->sender_account_id = $transaction_input['sender_account_id'];
        $transaction->recipient_account_id = $transaction_input['recipient_account_id'];
        $transaction->sender_user_id = $transaction_input['sender_user_id'];
        $transaction->recipient_user_id = $transaction_input['recipient_user_id'];
        $transaction->save();

        return response()->json([
            'data' => $transaction,
            'message' => 'Successfully made transaction.',
            'status' => 'Success'
        ], 201);
    }
}
