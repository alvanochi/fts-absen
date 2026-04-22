<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;


use App\Http\Helper\ResponseBuilder;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Login credentials are invalid.',
                    'data' => []
                ], 200);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Could not create token.',
                'data' => []
            ], 500);
        }

        //Token created, return with success response and jwt token
        $user = JWTAuth::user();

        return ResponseBuilder::success(200, "success", [
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [

        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return ResponseBuilder::success(200, "error", $validator->messages());
        }

        //Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function refresh(Request $request)
    {

        $validator = Validator::make($request->only('token'), []);
        if ($validator->fails()) {
            return ResponseBuilder::success(200, "error", $validator->messages());
        }
        return ResponseBuilder::success(200, "success", [
            'user' => JWTAuth::user(),
            'token' => JWTAuth::refresh(),
        ]);
    }

    public function get_user(Request $request)
    {
        $validator = Validator::make($request->only('token'), []);
        if ($validator->fails()) {
            return ResponseBuilder::success(200, "error", $validator->messages());
        }

        return ResponseBuilder::success(200, "success", JWTAuth::user());
    }
}
