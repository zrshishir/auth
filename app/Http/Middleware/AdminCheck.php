<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (Auth::user()->user_role == 'admin') {
            return $next($request);
        }

        $responseData['error'] = 1;
        $responseData['statusCode'] = 400;
        $responseData['errorMsg'] = "Only admin have the permission to access it.";
        $responseData['data'] = "" ;


        return response()->json($responseData);
    }
}
