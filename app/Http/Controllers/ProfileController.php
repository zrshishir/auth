<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helper\Helper;


class ProfileController extends Controller
{
    private $helping = "";

    public function __construct(Request $request){
        $this->helping = new Helper();
    }


    public function updateProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_name' => 'unique:users',
            'email' => 'unique:users',
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

            User::where('id', Auth::user()->id)
                ->update($request->all());

            DB::commit();
            $bug = 0;
        } catch (Exception $e){
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if($bug == 0){
            $responseData = $this->helping->responseProcess(0, 200, "Profile has been updated", ['user' => Auth::user()]);
            return response()->json($responseData);
        } elseif($bug == 1062){
            $responseData = $this->helping->responseProcess(1, 1062, "Data is found duplicate.", "");
            return response()->json($responseData);
        }else{
            $responseData = $this->helping->responseProcess(1, 1062, "something went wrong.", "");
            return response()->json($responseData);
        }
    }
}
