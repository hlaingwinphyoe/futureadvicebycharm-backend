<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BankController extends Controller
{
    private $mediaSvc;

    public function __construct(MediaService $mediaSvc)
    {
        $this->mediaSvc = $mediaSvc;
    }

    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $banks = Bank::query()
            ->filterOn()
            ->latest()
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($bank) => [
                'id' => $bank->id,
                'acc_name' => $bank->acc_name,
                'acc_no' => $bank->acc_no,
                'type' => $bank->type,
                'image' => $bank->media,
                'disabled' => $bank->disabled,
            ]);

        return Inertia::render('Admin/Bank/Index', [
            'banks' => $banks
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'acc_name' => 'required|string',
            'acc_no' => 'required|string',
            'type' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::transaction(function () use ($request) {
            $bank = Bank::create([
                'acc_name' => $request->acc_name,
                'acc_no' => $request->acc_no,
                'type' => $request->type,
            ]);

            if ($request->hasFile('image')) {
                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "bank",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $bank->update([
                    'image' => $url
                ]);
            }
        });

        return redirect()->back()->with('success', 'Successfully Created.');
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'acc_name' => 'required|string',
            'acc_no' => 'required|string',
            'type' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::transaction(function () use ($request, $bank) {
            $bank->update([
                'acc_name' => $request->acc_name,
                'acc_no' => $request->acc_no,
                'type' => $request->type,
            ]);

            if ($request->hasFile('image')) {

                if ($bank->image !== null) {
                    Storage::disk('public')->delete($bank->image);
                }

                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "bank",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $bank->update([
                    'image' => $url
                ]);
            }
        });

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function changeStatus(Bank $bank)
    {
        $data = DB::transaction(function () use ($bank) {
            if ($bank->disabled === 0) {
                $bank->update(['disabled' => 1]);
            } else {
                $bank->update(['disabled' => 0]);
            }
        });

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function destroy(Bank $bank)
    {
        DB::transaction(function () use ($bank) {
            Storage::disk('public')->delete($bank->image);
            $bank->delete();
        });

        return redirect()->back()->with('success', 'Successfully Deleted.');
    }
}
