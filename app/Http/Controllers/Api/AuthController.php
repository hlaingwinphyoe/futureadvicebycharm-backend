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
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            // 'phone' => 'required|numeric|unique:users,phone|digits_between:5,11',
            'username' => ['required', 'string', 'max:255', 'unique:users,name'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        try {
            $userRole = Role::where('slug', 'user')->first();

            $user = User::create([
                'name' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $userRole->id
            ]);

            $deviceName = $request->device_name ?? 'Default Device';

            // Create main access token with short expiry time
            $accessToken = $user->createToken($deviceName, ['*'], now()->addHour());

            // Create refresh token with longer expiry time
            $refreshToken = $user->createToken($deviceName . '_refresh', ['*'], now()->addDays(7));

            // $userAgent = $request->server('HTTP_USER_AGENT');
            // $ipAddress = $_SERVER['REMOTE_ADDR'];

            // $agent = new \Jenssegers\Agent\Agent;
            // $device = "";
            // if ($agent->isDesktop()) {
            //     $device = "Desktop";
            // } else if ($agent->isMobile()) {
            //     $device = "Mobile";
            // } else if ($agent->isTablet()) {
            //     $device = "Tablet";
            // }
            // $browser = $agent->browser();
            // $platform = $agent->platform();
            // LoginLog::create([
            //     'agent' => $userAgent,
            //     'device' => $device ? $device : null,
            //     'browser' => $browser ? $browser : null,
            //     'platform' => $platform ? $platform : null,
            //     'ip_address' => $ipAddress,
            //     'user_id' => $user->id
            // ]);

            return response()->json([
                'access_token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'user' =>  new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'credentials' => 'required|string',
            'password' => "required"
        ]);

        try {
            $user = User::whereAny(
                [
                    'name',
                    'email',
                    'phone'
                ],
                $request->credentials
            )->first();
            // $user = User::where('name', $request->credentials)
            //     ->orWhere('phone', $request->credentials)
            //     ->orWhere('email', $request->credentials)
            //     ->first();
            if ($user == null) {
                return [
                    'status' => false,
                    'code' => 403,
                    'message' => "User not found."
                ];
            }

            if (!Hash::check($request->password, $user->password)) {
                return [
                    'status' => false,
                    'code' => 403,
                    'message' => "These Credentials do not match our records."
                ];
            }

            // $userAgent = $request->server('HTTP_USER_AGENT');
            // $ipAddress = $_SERVER['REMOTE_ADDR'];

            // $agent = new \Jenssegers\Agent\Agent;
            // $device = "";
            // if ($agent->isDesktop()) {
            //     $device = "Desktop";
            // } else if ($agent->isMobile()) {
            //     $device = "Mobile";
            // } else if ($agent->isTablet()) {
            //     $device = "Tablet";
            // }
            // $browser = $agent->browser();
            // $platform = $agent->platform();
            // LoginLog::create([
            //     'agent' => $userAgent,
            //     'device' => $device ? $device : null,
            //     'browser' => $browser ? $browser : null,
            //     'platform' => $platform ? $platform : null,
            //     'ip_address' => $ipAddress,
            //     'user_id' => $user->id
            // ]);

            $deviceName = $request->device_name ?? 'Default Device';

            $accessToken = $user->createToken($deviceName, ['*'], now()->addHour());

            $refreshToken = $user->createToken($deviceName . '_refresh', ['*'], now()->addDays(7));

            return response()->json([
                'access_token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'user' =>  new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $refreshTokenParts = explode('|', $request->refresh_token, 2);

        if (count($refreshTokenParts) !== 2) {
            return response()->json(['message' => 'Invalid token format'], 401);
        }

        $refreshToken = PersonalAccessToken::findToken($refreshTokenParts[1]);

        if (!$refreshToken || $refreshToken->created_at->addDays(7)->isPast()) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        $user = $refreshToken->tokenable;
        $deviceName = explode('_refresh', $refreshToken->name)[0] ?? 'Default Device';

        $newAccessToken = $user->createToken($deviceName, ['*'], now()->addHour());

        // $refreshToken->delete();

        // Create new refresh token if needed
        // Uncomment the following if you want to issue a new refresh token each time
        /*
        $newRefreshToken = $user->createToken($deviceName . '_refresh', ['*'], now()->addDays(7));
        return response()->json([
            'access_token' => $newAccessToken->plainTextToken,
            'refresh_token' => $newRefreshToken->plainTextToken,
        ]);
        */

        return response()->json([
            'access_token' => $newAccessToken->plainTextToken,
        ]);
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
