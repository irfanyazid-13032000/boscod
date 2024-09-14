<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        // semua method butuh login dulu kecuali method untuk login dan register
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register()
    {
        $validator = Validator::make(request()->all(),[
            'username'=>'required|unique:users,username',
            'password'=>'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages());
        }

        $user = User::create([
            'username'=>request('username'),
            'password'=>Hash::make(request('password')),
        ]);

        if ($user) {
            return response()->json(['message' => 'berhasil menambahkan user']);
        }else{
            return response()->json(['message' => 'gagal menambahkan user']);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['username', 'password']);
    
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
    
        return $this->respondWithToken($token);
    }
    

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $accessTokenNew = auth()->refresh();


        return $this->respondWithToken($accessTokenNew);
    }



    

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($accessToken)
    {
    
        $response = [
            'accessToken' => $accessToken,
        ];
    
    
        return response()->json($response);
    }
    

}
