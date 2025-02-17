<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\MediaService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private $userSvc;
    private $mediaSvc;
    public function __construct(UserService $userSvc, MediaService $mediaSvc)
    {
        $this->userSvc = $userSvc;
        $this->mediaSvc = $mediaSvc;
    }

    public function updateInfo(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $request->validate([
            'username' => 'required|string|max:255|unique:users,name,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'dob' => 'nullable|date|date_format:Y-m-d|before:tomorrow',
            'gender' => 'nullable|numeric|exists:statuses,id',
            'birthday' => 'nullable|numeric|exists:statuses,id',
            'phone' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $user = $this->userSvc->updateInfo($user, $request->all());
            DB::commit();

            $user = new UserResource($user);

            return $this->sendResponse($user, 'Success!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 422);
        }
    }

    public function updatePassword(Request $request)
    {
        $currentUser = User::findOrFail(Auth::id());
        if ($currentUser) {
            $request->validate([
                'currentPassword' => 'required|string',
                'newPassword' => 'required|string|min:6',
                'confirmPassword' => 'required_with:newPassword|same:newPassword|min:6',
            ]);
            try {
                DB::beginTransaction();
                if (!$currentUser || !Hash::check($request->currentPassword, $currentUser->password)) {
                    return $this->sendError("These credentials do not match our records", 422);
                } else {
                    $currentUser->password = Hash::make($request->newPassword);
                    $currentUser->update();

                    $currentUser = new UserResource($currentUser);
                    return $this->sendResponse($currentUser, 'Success');
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->sendError($e->getMessage(), 422);
            }
        } else {
            return $this->sendError('Unauthenticated.', 404);
        }
    }

    public function uploadAvatar(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        try {
            DB::beginTransaction();

            if ($user->profile_photo_path !== null) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $mediaFormdata = [
                'media' => $request->file('avatar'),
                'type' => "profile",
            ];

            $url = $this->mediaSvc->storeMedia($mediaFormdata);

            $user->update([
                'profile_photo_path' => $url
            ]);

            DB::commit();
            $user = new UserResource($user);

            return $this->sendResponse($user, 'Success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 422);
        }
    }
}
