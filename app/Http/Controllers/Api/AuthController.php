<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username' => 'Username tidak terisi',
            'password' => 'Password tidak terisi',
        ]);

        // 2. Cek Credentials
        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json([
                'message' => 'username atau password salah'
            ], 401);
        }

        // 3. Ambil User & Buat Token Sanctum
        $user = User::where('username', $request->username)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Return Token ke Frontend
        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
}