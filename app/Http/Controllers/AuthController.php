<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
//        Log::info(json_encode($request->all()));

        $registerUserData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'phone_number' => 'required|integer',
            'password' => 'required|min:5',
        ]);
        $user = User::create([
            'name' => $registerUserData['name'],
            'email' => $registerUserData['email'],
            'phone_number' => $registerUserData['phone_number'],
            'password' => Hash::make($registerUserData['password']),
        ]);
        return response()->json([
            'message' => 'Utilisateur cree avec succes '
        ], 200);
    }


    public function login(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:5'
        ]);
        $user = User::where('email', $loginUserData['email'])->first();
        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'données invalides'
            ], 401);
        }
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                "message" => "Déconnecté avec succès"
            ], 200);
        }else{
            return response()->json([
                "message" => "utilisateur Non Authentifie"
            ], 401);

        }
    }
}
