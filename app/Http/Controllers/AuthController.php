<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Helper\Helper;
use Validator, DB;
use App\Models \User;

class AuthController extends Controller
{
    private $helping = "";

    public function __construct(Request $request){
        $this->helping = new Helper();
    }

    public function register(Request $request): JsonResponse{
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'user_name' => 'required|string',
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
                $success['token'] =  $user->createToken('MyApp')->accessToken;
                $user['api_token'] = $success['token']->token;
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

        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){

            $user = Auth::user();

            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $user['api_token'] = $success['token']->token;
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


    public function unAuthMessage(Request $request): JsonResponse{
        $responseData = $this->helping->responseProcess(1, 401, "Sorry, you are not logged in.", "");
        return response()->json($responseData);
    }
}
