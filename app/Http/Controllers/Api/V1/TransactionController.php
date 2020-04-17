<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TrasactionResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            'data' => new TrasactionResource($transactions),
            'message' => 'Successfully loaded all transactions for user: '.$this->user->name,
            'status' => 'Success'
        ]);
    }
}
