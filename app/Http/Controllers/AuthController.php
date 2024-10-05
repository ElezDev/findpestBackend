<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Método para registrar un nuevo usuario

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Crear la persona relacionada
        Person ::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'phone_number' => $validatedData['phone_number'],
            'address' => $validatedData['address'],
            'email' => $validatedData['email'],
            'user_id' => $user->id,
        ]);

        // Generar el token JWT
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }


    // Método para autenticar un usuario
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $credentials = $request->only('email', 'password');
    
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
    
        return response()->json(compact('token'));
    }

    // Método para cerrar sesión
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'User logged out successfully']);
    }



    public function getAuthenticatedUser()
    {
        try {
            if (!$user = Auth::user()) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->load('persona');

            return response()->json([
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not fetch user'], 500);
        }
    }

}