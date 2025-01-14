<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'phone' => 'required|numeric|unique:users,phone|digits_between:5,11',
            'email' => "nullable|email|unique:users,email",
            'password' => "required|string|min:8|max:255",
        ]);

        $userRole = Role::where('slug', 'user')->first();

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            "password" => Hash::make($request->password),
            'role_id' => $userRole->id
        ]);

        if (Auth::attempt($request->only(['phone', 'password']))) {
            $token = Auth::user()->createToken('access_token')->plainTextToken;
            return response()->json([
                'token' => $token,
                'user' => new UserResource($user)
            ], 200);
        }
        return response()->json(['message' => "User Not Found."], 401);
    }

    public function login(Request $request)
    {
        $request->validate([
            'credentials' => 'required|string',
            'password' => "required"
        ]);

        $user = User::where('name', $request->credentials)
            ->orWhere('phone', $request->credentials)
            ->orWhere('email', $request->credentials)
            ->first();

        if ($user->tokens()->where('tokenable_id', $user->id)->exists()) {
            $user->tokens()->delete();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'These Credentials do not match.'
            ], 422);
        }

        $token = $user->createToken('access_token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' =>  new UserResource($user)
        ], 200);
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        }
        return response()->json(['Successfully logout.']);
    }
}
