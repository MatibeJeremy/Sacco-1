<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TrasactionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('role:ADMIN');
    }

    public function index()
    {
        return response()->json([
            'data' => UserResource::collection(User::all()),
            'message' => 'Successfully loaded all users.',
            'status' => 'Success'
        ]);
    }

}
