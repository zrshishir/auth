<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Helper\Helper;
use Validator, DB;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\Code;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    private $helping = "";

    public function __construct(Request $request){
        $this->helping = new Helper();
    }

    public function sendMail(Request  $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|unique:users|regex:/(.+)@(.+)\.(.+)/i'
        ]);

        if($validator->fails()){
            $errors = $validator->errors();
            $errorMsg = "";
            foreach ($errors->all() as $msg) {
                $errorMsg .= $msg;
            }
            $responseData = $this->helping->responseProcess(1, 422, $errorMsg, "");

            return response()->json($responseData);
        }
        $email = $request->email;

        $details = [
            'title' => 'Please click the link below and sign up',
            'body'  => url('api/signup?email=') . $email,
            'code'  => 0,
        ];

        Mail::to($email)->send(new SendMail($details));


        $responseData = $this->helping->responseProcess(0, 200, "Email has been successfully sent.", "");
        return response()->json($responseData);
    }

    public function userRegister(Request $request): JsonResponse{

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|unique:users',
            'email' => 'required|string|unique:users|email',
            'password' => 'required|string|confirmed'
        ]);


        if($validator->fails()){
            $errors = $validator->errors();
            $errorMsg = "";
            foreach ($errors->all() as $msg) {
                $errorMsg .= $msg;
            }
            $responseData = $this->helping->responseProcess(1, 422, $errorMsg, "");
            return response()->json($responseData);
        }

        $email = $request->email;
        $code = random_int(100000, 999999);
        $details = [
            'title' => 'Please submit the code to complete the registration',
            'body'  => "",
            'code'  => $code
        ];

        Mail::to($email)->send(new SendMail($details));

        try {
            DB::beginTransaction();

            $user = new User([
                'user_name' => $request->user_name,
                'email' => $email,
                'password' => bcrypt($request->password),
                'user_role' => "user"
            ]);

            $user->save();

            $codeInsert = new Code([
                'user_id' => $user->id,
                'code' => $code
            ]);

            $codeInsert->save();

            DB::commit();
            $bug = 0;
        } catch (Exception $e){
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if($bug == 0){
                $responseData = $this->helping->responseProcess(0, 200, "6 digit code has been sent to your email. Please submit the code to activate the account.", "");
                return response()->json($responseData);
        } elseif($bug == 1062){
            $responseData = $this->helping->responseProcess(1, 1062, "Data is found duplicate.", "");
            return response()->json($responseData);
        }else{
            $responseData = $this->helping->responseProcess(1, 1062, "something went wrong.", "");
            return response()->json($responseData);
        }
    }

    public function codeVerification(Request $request) {
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:6',
        ]);

        if($validator->fails()){
            $errors = $validator->errors();
            $errorMsg = "";
            foreach ($errors->all() as $msg) {
                $errorMsg .= $msg;
            }
            $responseData = $this->helping->responseProcess(1, 422, $errorMsg, "");
            return response()->json($responseData);
        }

        $code = $request->code;
        $codeExistency = Code::with('user')
                                ->where('code', $code)
                                ->where('active', 1)
                                ->first();
        if(! $codeExistency){
            $responseData = $this->helping->responseProcess(1, 403, "The code has been expired.", "");
            return response()->json($responseData);
        }

        try {
            DB::beginTransaction();

            Code::where('code', $code)
                    ->where('user_id', $codeExistency->user_id)
                    ->update([
                        'active' => 0
                    ]);

            User::where('id', $codeExistency->user_id)
                    ->update([
                        'registered_at' => Carbon::now(),
                        'active' => 1
                    ]);

            DB::commit();
            $bug = 0;
        } catch (Exception $e){
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if($bug == 0){
            $responseData = $this->helping->responseProcess(0, 200, "Your account has been verified and activated. Please, login with your email and password.", "");
            return response()->json($responseData);
        } elseif($bug == 1062){
            $responseData = $this->helping->responseProcess(1, 1062, "Data is found duplicate.", "");
            return response()->json($responseData);
        }else{
            $responseData = $this->helping->responseProcess(1, 1062, "something went wrong.", "");
            return response()->json($responseData);
        }
    }

    public function register(Request $request): JsonResponse{
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);


        if($validator->fails()){
            $errors = $validator->errors();
            $errorMsg = "";
            foreach ($errors->all() as $msg) {
                $errorMsg .= $msg;
            }
            $responseData = $this->helping->responseProcess(1, 422, $errorMsg, "");

            return response()->json($responseData);
        }

        try {
            DB::beginTransaction();

            $user = new User([
                'name' => $request->name,
                'user_name' => $request->user_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'active'=>1,
                'user_role' => "user"
            ]);

            $user->save();

            DB::commit();
            $bug = 0;
        } catch (Exception $e){
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if($bug == 0){
            if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
                $user = Auth::user();
                $success['token'] =  $user->createToken('LaravelAuthApp')->accessToken;
                $user['api_token'] = $success['token'];
                $user['token_type'] = "Bearer";

                $responseData = $this->helping->responseProcess(0, 200, "Your are logged in", ['users' => $user]);
                return response()->json($responseData);
            }
            else{
                $responseData = $this->helping->responseProcess(1, 401, "You have entered an incorrect Phone No/Password combination.", "");
                return response()->json($responseData);
            }
        } elseif($bug == 1062){
            $responseData = $this->helping->responseProcess(1, 1062, "Data is found duplicate.", "");
            return response()->json($responseData);
        }else{
            $responseData = $this->helping->responseProcess(1, 1062, "something went wrong.", "");
            return response()->json($responseData);
        }
    }


    public function login(Request $request): JsonResponse{
        $user = User::where('email', $request->email)->first();
        if(! $user){
            $responseData = $this->helping->responseProcess(1, 401, "User does not exist. Please Sign Up.", "");
            return response()->json($responseData);
        }

        if(Auth::attempt(['email' => request('email'), 'password' => request('password'), 'active' => 1])){

            $user = Auth::user();

            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $user['api_token'] = $success['token'];
            $user['token_type'] = "Bearer";

            $responseData = $this->helping->responseProcess(0, 200, "Your are logged in", [
                'users' => $user
            ]);

            return response()->json($responseData);
        }
        else{
            $responseData = $this->helping->responseProcess(1, 401, "incorrect Password", "");
            return response()->json($responseData);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $loggedInChecked = Auth::check();

        $request->user()->token()->revoke();
        $responseData = $this->helping->responseProcess(0, 200, "Successfully logged out", "");
        return response()->json($responseData);
    }


    public function unAuthMessage(): JsonResponse{
        return response()->json('shishir');
        $responseData = $this->helping->responseProcess(1, 401, "Sorry, you are not logged in.", "");
        return response()->json($responseData);
    }
}
