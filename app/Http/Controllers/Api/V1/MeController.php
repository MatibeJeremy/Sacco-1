<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MeController extends Controller
{
    protected $currentUser;

    public function __construct()
    {
        $this->currentUser = auth()->user();
    }

    public function update(Request $request)
    {
        $user = $this->currentUser;

        $this->validate($request, [
            'name' => 'required|string',
            'phoneNumber' => 'required',
            'about' => 'string',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
        ]);
        $request->user()->fill([
            'name' => $request->name
        ])->save();
        $request->user()->fill([
            'phoneNumber' => $request->phoneNumber
        ])->save();
        $request->user()->fill([
            'about' => $request->about
        ])->save();

        $request->user()->fill([
            'email' => $request->email
        ])->save();

        return response()->json([
            'message' => 'Successfully updated info',
            'status' => 'Success'
        ], 200);
    }


}
