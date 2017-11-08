<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class PassportController extends Controller
{
    public function register(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'name' => 'required',
    		'email' => 'required|email',
    		'password' => 'required',
    		'c_password' => 'required|same:password'
    	]);

    	if ($validator->fails()) {
    		return response()->json(['error' => $validator->errors()], 401);
    	}

    	$input = $request->all();
    	$input['password'] = bcrypt($input['password']);

    	$user = User::create($input);
    	$success['token'] = $user->createToken('My App')->accessToken;
    	$success['name'] = $user->name;

    	return response()->json(['success' => $success], 200);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('My App')->accessToken;
            return response()->json(['success' => $success], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function getAuthenticatedUser()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], 200);
    }
}
