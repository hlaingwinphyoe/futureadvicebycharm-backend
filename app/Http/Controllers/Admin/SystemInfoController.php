<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\SystemInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SystemInfoController extends Controller
{
    public function index()
    {
        $system_info = SystemInfo::with('phones')->first();
        return Inertia::render('Admin/SystemInfo/Index', [
            'system_info' => $system_info
        ]);
    }

    public function updateInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email',
            'address' => 'required|string',
        ]);

        $info = SystemInfo::latest()->first();

        $info->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address
        ]);

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function addPhone(Request $request, SystemInfo $info)
    {
        $request->validate([
            'phone' => "required|string",
        ]);

        try {
            DB::beginTransaction();

            $phone = Phone::create([
                'phone' => $request->phone,
                'priority' => $request->priority ?? 0
            ]);

            $phone->system_infos()->sync($info);

            DB::commit();

            return redirect()->back()->with('success', 'Phone Number Added.');
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function deletePhone(SystemInfo $info, Phone $phone)
    {
        DB::transaction(function () use ($info, $phone) {
            $info->phones()->detach($phone->id);

            return redirect()->back()->with('success', 'Phone Number Deleted.');
        });
    }
}
