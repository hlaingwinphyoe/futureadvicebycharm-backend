<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StaffController extends Controller
{
    private $mediaSvc;
    public function __construct(MediaService $mediaSvc)
    {
        $this->mediaSvc = $mediaSvc;
    }

    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $staffs = User::query()
            ->notAdmin()
            ->notCustomer()
            ->filterOn()
            ->latest()
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($staff) => [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'role_id' => $staff->role_id,
                'role' => $staff->role ? $staff->role->name : '',
                'profile' => $staff->profile
            ]);

        $roles = Role::notDeveloper()->orderBy('name')->get();

        return Inertia::render('Admin/Staff/Index', [
            'staffs' => $staffs,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'role' => 'required|numeric|exists:roles,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|digits_between:5,13',
            'social_link' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024'
        ]);

        try {
            DB::beginTransaction();

            $staff = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'social_link' => $request->social_link,
                'role_id' => $request->role,
                'password' => Hash::make('password-123')
            ]);

            if ($request->hasFile('image')) {
                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "user",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $staff->update([
                    'profile_photo_path' => $url
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Successfully Created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, User $staff)
    {
        $request->validate([
            'name' => 'required|string',
            'role' => 'required|numeric|exists:roles,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|digits_between:5,13',
            'social_link' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024'
        ]);

        try {
            DB::beginTransaction();

            $staff->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'social_link' => $request->social_link,
                'role_id' => $request->role,
            ]);

            if ($request->hasFile('image')) {
                // Delete the old image
                if ($staff->profile_photo_path !== null) {
                    Storage::disk('public')->delete($staff->profile_photo_path);
                }

                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "user",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $staff->update([
                    'profile_photo_path' => $url
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Successfully Updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function changePassword(Request $request, User $staff)
    {
        $request->validate([
            'password' => 'required|min:8|string'
        ]);
        DB::transaction(function () use ($staff, $request) {
            $staff->update([
                'password' => Hash::make($request->password)
            ]);
        });

        return redirect()->back()->with('success', 'Successfully Changed.');
    }

    public function destroyMedia(User $staff)
    {
        DB::transaction(function () use ($staff) {
            Storage::disk('public')->delete($staff->profile_photo_path);
            $staff->update(['profile_photo_path' => null]);
        });
        return redirect()->back();
    }

    public function destroy(User $staff)
    {
        DB::transaction(function () use ($staff) {
            $staff->delete();
        });

        return redirect()->back()->with('success', 'Successfully Deleted.');
    }
}
