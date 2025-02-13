<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\LoginLog;
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
        if ($user == null) {
            return [
                'status' => false,
                'code' => 403,
                'message' => "User not found."
            ];
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'These Credentials do not match our records.'
            ], 422);
        }

        $userAgent = $request->server('HTTP_USER_AGENT');
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $agent = new \Jenssegers\Agent\Agent;
        $device = "";
        if ($agent->isDesktop()) {
            $device = "Desktop";
        } else if ($agent->isMobile()) {
            $device = "Mobile";
        } else if ($agent->isTablet()) {
            $device = "Tablet";
        }
        $browser = $agent->browser();
        $platform = $agent->platform();
        LoginLog::create([
            'agent' => $userAgent,
            'device' => $device ? $device : null,
            'browser' => $browser ? $browser : null,
            'platform' => $platform ? $platform : null,
            'ip_address' => $ipAddress,
            'user_id' => $user->id
        ]);

        $user->tokens()->where('name', $device)->delete();

        $accessToken = $user->createToken($device, ['*'], now()->addHours(1))->plainTextToken;
        $refreshToken = $user->createToken($device . '_refresh', ['refresh'], now()->addDays(7))->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' =>  new UserResource($user)
        ], 200);
    }

    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required'
        ]);

        $user = $request->user();

        foreach ($user->tokens as $token) {
            $token->revoke();
            $token->delete();
        }

        $agent = new \Jenssegers\Agent\Agent;
        $device = "";
        if ($agent->isDesktop()) {
            $device = "Desktop";
        } else if ($agent->isMobile()) {
            $device = "Mobile";
        } else if ($agent->isTablet()) {
            $device = "Tablet";
        }

        // Generate a new token
        $newAccessToken = $user->createToken(
            $device,
            ['*'],
            now()->addHours(1)
        );

        return response()->json([
            'access_token' => $newAccessToken,
        ], 200);
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        }
        return response()->json(['Successfully logout.']);
    }

    public function getUser(Request $request)
    {
        $user = $request->user();

        $user = new UserResource($user);

        return $user;
    }
}
