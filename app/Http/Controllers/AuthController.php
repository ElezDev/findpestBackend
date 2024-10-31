<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Log;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Método para registrar un nuevo usuario

    // public function register(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'required|string|email|unique:users',
    //         'password' => 'required|string|min:6',
    //         'first_name' => 'required|string',
    //         'last_name' => 'required|string',
    //         'phone_number' => 'nullable|string',
    //         'address' => 'nullable|string',
    //     ]);

    //     // Crear el usuario
    //     $user = User::create([
    //         'name' => $validatedData['name'],
    //         'email' => $validatedData['email'],
    //         'password' => Hash::make($validatedData['password']),
    //     ]);

    //     // Crear la persona relacionada
    //     Person ::create([
    //         'first_name' => $validatedData['first_name'],
    //         'last_name' => $validatedData['last_name'],
    //         'phone_number' => $validatedData['phone_number'],
    //         'address' => $validatedData['address'],
    //         'email' => $validatedData['email'],
    //         'user_id' => $user->id,
    //     ]);

    //     // Generar el token JWT
    //     $token = JWTAuth::fromUser($user);

    //     return response()->json([
    //         'user' => $user,
    //         'token' => $token,
    //     ], 201);
    // }





    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'last_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'biography' => 'nullable|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        try {
            // Crear usuario
            $user = User::create([
                'name' => $validatedData['first_name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);
    
            $profilePicturePath = '';
            if ($request->hasFile('image_url')) {
                $imageName = $validatedData['first_name'] . '_' . $validatedData['last_name'] . '_' . time() . '.' . $request->file('image_url')->getClientOriginalExtension();
                $profilePicturePath = $request->file('image_url')->storeAs('profile_image', $imageName, 'public');
                $profilePicturePath = Storage::url($profilePicturePath);
            }
    
    
            $person = Person::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'phone_number' => $validatedData['phone_number'] ?? '',
                'address' => $validatedData['address'] ?? '',
                'email' => $validatedData['email'],
                'biography' => $validatedData['biography'] ?? '',
                'user_id' => $user->id,
                'image_url' => $profilePicturePath,
            ]);
    
            // Crear token de autenticación JWT
            $token = JWTAuth::fromUser($user);
    
            return response()->json([
                'user' => $user,
                'person' => $person,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear la persona: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo crear el usuario y la persona'], 500);
        }
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