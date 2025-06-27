<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Package;
use App\Models\Remark;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PackageController extends Controller
{
    private $mediaSvc;
    public function __construct(MediaService $mediaSvc)
    {
        $this->mediaSvc = $mediaSvc;
    }

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
                'image' => $package->media,
                'discount_percent' => $package->discount_percent,
                'final_price' => $package->final_price,
                'th_final_price' => $package->th_final_price
            ]);

        $astrologers = User::astrologer()->get();
        $currencies = Currency::latest()->get();

        return Inertia::render('Admin/Package/Index', [
            'packages' => $packages,
            'astrologers' => $astrologers,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Calculate discounted prices
     */
    private function calculateDiscountedPrices($price, $th_price, $discount)
    {
        if ($discount > 0) {
            $final_price = round($price * (1 - $discount / 100), 2);
            $th_final_price = round($th_price * (1 - $discount / 100), 2);
        } else {
            $final_price = $price;
            $th_final_price = $th_price;
        }
        return [
            'final_price' => $final_price,
            'th_final_price' => $th_final_price,
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:packages,name',
            'price' => 'required|numeric|min:50',
            'th_price' => 'required|numeric|min:0',
            'currency' => 'nullable|numeric|exists:currencies,id',
            'astrologer' => 'required|numeric|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
            'discount_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $discount = $request->discount_percent ?? 0;
        $price = $request->price;
        $th_price = $request->th_price;
        $discounted = $this->calculateDiscountedPrices($price, $th_price, $discount);

        $data = DB::transaction(function () use ($request, $discount, $discounted) {
            $th_currency = Currency::where('slug', 'thb')->first();
            $mm_currency = Currency::where('slug', 'mmk')->first();
            $package = Package::create([
                'name' => $request->name,
                'price' => $request->price,
                'th_price' => $request->th_price,
                'currency_id' => $mm_currency->id,
                'th_currency_id' => $th_currency->id,
                'astrologer_id' => $request->astrologer,
                'discount_percent' => $discount,
                'final_price' => $discounted['final_price'],
                'th_final_price' => $discounted['th_final_price'],
            ]);

            if ($request->hasFile('image')) {
                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "package",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $package->update([
                    'image' => $url
                ]);
            }
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
            'astrologer' => 'required|numeric|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
            'discount_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $discount = $request->discount_percent ?? 0;
        $price = $request->price;
        $th_price = $request->th_price;
        $discounted = $this->calculateDiscountedPrices($price, $th_price, $discount);

        $data = DB::transaction(function () use ($request, $package, $discount, $discounted, $price, $th_price) {
            $th_currency = Currency::where('slug', 'thb')->first();
            $mm_currency = Currency::where('slug', 'mmk')->first();

            $package->update([
                'name' => $request->name,
                'price' => $price,
                'th_price' => $th_price,
                'currency_id' => $mm_currency->id,
                'th_currency_id' => $th_currency->id,
                'astrologer_id' => $request->astrologer,
                'discount_percent' => $discount,
                'final_price' => $discounted['final_price'],
                'th_final_price' => $discounted['th_final_price'],
            ]);

            if ($request->hasFile('image')) {
                // Delete the old image
                if ($package->image !== null) {
                    Storage::disk('public')->delete($package->image);
                }
                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "package",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $package->update([
                    'image' => $url
                ]);
            }
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

    public function getRemarks($id)
    {
        $package = Package::with('remarks')->findOrFail($id);
        return response()->json([
            'remarks' => $package->remarks
        ]);
    }

    public function addRemarks(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_image' => 'required|boolean'
        ]);
        $data = DB::transaction(function () use ($package, $request) {
            $remark = Remark::create([
                'name' => $request->name,
                'is_image' => $request->is_image
            ]);

            $package->remarks()->attach($remark->id);
        });

        return redirect()->back()->with('success', 'Remarks added successfully.');
    }

    public function destroyMedia(Package $package)
    {
        $data = DB::transaction(function () use ($package) {
            Storage::disk('public')->delete($package->image);
            $package->update(['image' => null]);
        });
        return redirect()->back();
    }

    public function destroy(Package $package)
    {
        if ($package->appointment_packages()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'This package have appointment']);
        }
        $data = DB::transaction(function () use ($package) {
            $package->delete();
        });

        return redirect()->back()->with('success', 'Successfully Deleted.');
    }

    public function bulkDiscount(Request $request)
    {
        $request->validate([
            'discount_percent' => 'required|integer|min:0|max:100',
        ]);
        try {
            DB::beginTransaction();
            $discount = $request->discount_percent;
            $packages = Package::all();
            foreach ($packages as $package) {
                $discounted = $this->calculateDiscountedPrices($package->price, $package->th_price, $discount);
                $package->update([
                    'discount_percent' => $discount,
                    'final_price' => $discounted['final_price'],
                    'th_final_price' => $discounted['th_final_price'],
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'All packages updated with discount.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function deleteRemark(Package $package, Remark $remark)
    {
        DB::transaction(function () use ($package, $remark) {
            $package->remarks()->detach($remark->id);
            $remark->delete();
        });
        return redirect()->back()->with('success', 'Successfully Deleted.');
    }
}
