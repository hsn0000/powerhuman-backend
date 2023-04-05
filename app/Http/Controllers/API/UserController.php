<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

use App\Models\User;

class UserController extends Controller
{
    
    /**
     * login
     * 
     * @param Request
     * @return \JsonResponse
     * */ 
    public function login(Request $request)
    {
        try {
            // validate request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            // find user by email
            $credential = request(['email', 'password']);
            if(!Auth::attempt($credential)) {
                return ResponseFormatter::error('Unauthorized', 401);
            }

            $user = User::where('email', $request->email)->first();
            if(!Hash::check($request->password, $user->password)) {
                throw new Exception("invalid Password");
                
            }

            // generate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // return response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'Login Success');

        } catch (\Exception $te) {
           return ResponseFormatter::error('Authentication Failed');
        }
    }

    
    /**
     * register
     * 
     * @param Request
     * @return \JsonResponse
     * */ 
    public function register(Request $request)
    {
        try {
            // validate request
            $request->validate([
                'name' => ['required','string','max:255'],
                'email' => ['required','string','email','max:255','unique:users'],
                'password' => ['required','string','max:255', new Password],
            ]);

            // create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // generate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // return response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'Register Success');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage() ?? 'Authentication Failed');
        }
    }

    /**
     * logout
     * 
     * @param Request
     * @return \JsonResponse
     * */ 
    public function logout(Request $request)
    {
        // revoke token
        $token = $request->user()->currentAccessToken()->delete();

        // return response
        return ResponseFormatter::success($token, 'Logout success');
    }

    /**
     * fetch user
     * 
     * @param Request
     * @return \JsonResponse
     * */ 
    public function fetch(Request $request)
    {
        // Get user
        $user = $request->user();

        // return response
        return ResponseFormatter::success($user, 'Fetch success');
    }
    
}
