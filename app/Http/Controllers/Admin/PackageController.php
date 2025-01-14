<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $packages = Package::query()
            ->with('astrologer')
            ->filterOn()
            ->latest()
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($package) => [
                'id' => $package->id,
                'name' => $package->name,
                'price' => $package->price,
                'th_price' => $package->th_price,
                'currency_id' => $package->currency_id,
                'currency' => $package->currency ? $package->currency->name : 'Ks',
                'th_currency_id' => $package->th_currency_id,
                'th_currency' => $package->th_currency ? $package->th_currency->name : '฿',
                'astrologer' => $package->astrologer ? $package->astrologer->name : '',
                'astrologer_id' => $package->astrologer_id,
                'created_at' => $package->created_at->diffForHumans(),
                'disabled' => $package->disabled,
            ]);

        $astrologers = User::astrologer()->get();
        $currencies = Currency::latest()->get();

        return Inertia::render('Admin/Package/Index', [
            'packages' => $packages,
            'astrologers' => $astrologers,
            'currencies' => $currencies,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:packages,name',
            'price' => 'required|numeric|min:50',
            'th_price' => 'required|numeric|min:0',
            'currency' => 'nullable|numeric|exists:currencies,id',
            'astrologer' => 'required|numeric|exists:users,id'
        ]);

        $data = DB::transaction(function () use ($request) {
            $th_currency = Currency::where('slug', 'thb')->first();
            $mm_currency = Currency::where('slug', 'mmk')->first();
            $package = Package::create([
                'name' => $request->name,
                'price' => $request->price,
                'th_price' => $request->th_price,
                'currency_id' => $mm_currency->id,
                'th_currency_id' => $th_currency->id,
                'astrologer_id' => $request->astrologer,
            ]);
        });

        return redirect()->back()->with('success', 'Successfully Created.');
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|unique:packages,name,' . $package->id,
            'price' => 'required|numeric|min:50',
            'th_price' => 'required|numeric|min:0',
            'currency' => 'nullable|numeric|exists:currencies,id',
            'astrologer' => 'required|numeric|exists:users,id'
        ]);

        $data = DB::transaction(function () use ($request, $package) {
            $th_currency = Currency::where('slug', 'thb')->first();
            $mm_currency = Currency::where('slug', 'mmk')->first();
            $package->update([
                'name' => $request->name,
                'price' => $request->price,
                'th_price' => $request->th_price,
                'currency_id' => $mm_currency->id,
                'th_currency_id' => $th_currency->id,
                'astrologer_id' => $request->astrologer,
            ]);
        });

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function changeStatus(Package $package)
    {
        $data = DB::transaction(function () use ($package) {
            if ($package->disabled === 0) {
                $package->update(['disabled' => 1]);
            } else {
                $package->update(['disabled' => 0]);
            }
        });

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function destroy(Package $package)
    {
        $data = DB::transaction(function () use ($package) {
            $package->delete();
        });

        return redirect()->back()->with('success', 'Successfully Deleted.');
    }
}
