<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Requests\ChangePasswordRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function sendMail(Request $request){

        if(!$this->validateEmail($request->email)){
            return $this->failedResponse();
        }

        $this->send($request->email);
        return $this->successResponse();
    }

    public function send($email){

        $token = $this->createToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }

    public function createToken($email){
        $old_token = DB::table('password_resets')->where('email',$email)->first();
        if($old_token){
            return $old_token;
        }
        $token = str_random(60);

        $this->saveToken($token,$email);
        return $token;
    }

    public function saveToken($token, $email){
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    public function validateEmail($email){

        return !!User::where('email',$email)->first();
    }

    public function failedResponse(){
        return response()->json([
            'error' => 'email does not exist on our database'
        ], 404);
    }

    public function successResponse(){
        return response()->json([
            'success' => 'email has been sent. check your inbox'
        ], 200);
    }

    //change password functionality

    public function process(ChangePasswordRequest $request)
    {
       return $this->getPasswordResetTableRow($request)->count()>0 ? $this->changePassword($request) :
           $this->tokenNotFoundResponse();
    }

    public function getPasswordResetTableRow($request)
    {
        return DB::table('password_resets')->where(
            [
                'email' => $request->email,
                'token' => $request->resetToken]);
    }

    private function tokenNotFoundResponse(){
        return response()->json(['error' => 'Token or Email is incorrect'],
        401);
    }

    private function changePassword($request){
        $user = User::whereEmail($request->email)->first();
        $user->update(['password'=> bcrypt($request->password)]);
        $this->getPasswordResetTableRow($request)->delete();
        return response()->json([
            'data' => 'Password Successfully Changed'
        ], 202);
    }
}
