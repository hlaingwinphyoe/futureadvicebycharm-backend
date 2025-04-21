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
                // 'phone' => $request->phone,
                'email' => $request->email,
                "password" => Hash::make($request->password),
                'role_id' => $userRole->id
            ]);

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

            $accessToken = $user->createToken('tarotbycharm')->plainTextToken;
            $refreshToken = $user->createToken('tarotbycharm')->plainTextToken;

            return response()->json([
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
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

            $user->tokens()->delete();

            $accessToken = $user->createToken('tarotbycharm', ['*'], now()->addHours(1))->plainTextToken;
            $refreshToken = $user->createToken('tarotbycharm' . '_refresh', ['refresh'], now()->addDays(7))->plainTextToken;

            return response()->json([
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'user' =>  new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 404);
        }
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

        // $agent = new \Jenssegers\Agent\Agent;
        // $device = "";
        // if ($agent->isDesktop()) {
        //     $device = "Desktop";
        // } else if ($agent->isMobile()) {
        //     $device = "Mobile";
        // } else if ($agent->isTablet()) {
        //     $device = "Tablet";
        // }

        // Generate a new token
        $newAccessToken = $user->createToken('tarotbycharm')->plainTextToken;

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
