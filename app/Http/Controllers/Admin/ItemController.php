<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Item;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ItemController extends Controller
{
    private $mediaSvc;
    public function __construct(MediaService $mediaSvc)
    {
        $this->mediaSvc = $mediaSvc;
    }

    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $items = Item::query()
            ->with('user')
            ->filterOn()
            ->latest()
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($item) => [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'price' => $item->price,
                'th_price' => $item->th_price,
                'currency_id' => $item->currency_id,
                'currency' => $item->currency ? $item->currency->name : 'MMK',
                'th_currency_id' => $item->th_currency_id,
                'th_currency' => $item->th_currency ? $item->th_currency->name : '฿',
                'owner' => $item->owner,
                'created_at' => $item->created_at->diffForHumans(),
                'disabled' => $item->disabled,
                'image' => $item->media,
            ]);

        // $astrologers = Status::isType('')->get();
        $currencies = Currency::latest()->get();

        return Inertia::render('Admin/Item/Index', [
            'items' => $items,
            'currencies' => $currencies,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'th_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024'
        ]);

        try {
            DB::beginTransaction();
            $th_currency = Currency::where('slug', 'thb')->first();
            $mm_currency = Currency::where('slug', 'mmk')->first();

            $item = Item::create([
                'name' => $request->name,
                'price' => $request->price,
                'th_price' => $request->th_price,
                'currency_id' => $mm_currency->id,
                'th_currency_id' => $th_currency->id,
                'user_id' => Auth::id()
            ]);

            if ($request->hasFile('image')) {
                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "item",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $item->update([
                    'image' => $url
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Successfully Created.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'th_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024'
        ]);

        try {
            DB::beginTransaction();
            $th_currency = Currency::where('slug', 'thb')->first();
            $mm_currency = Currency::where('slug', 'mmk')->first();
            $item->update([
                'name' => $request->name,
                'price' => $request->price,
                'th_price' => $request->th_price,
                'currency_id' => $mm_currency->id,
                'th_currency_id' => $th_currency->id,
                'user_id' => Auth::id()
            ]);

            if ($request->hasFile('image')) {
                // Delete the old image
                if ($item->image !== null) {
                    Storage::disk('public')->delete($item->image);
                }
                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "item",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $item->update([
                    'image' => $url
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Successfully Updated.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function changeStatus(Item $item)
    {
        $data = DB::transaction(function () use ($item) {
            if ($item->disabled === 0) {
                $item->update(['disabled' => 1]);
            } else {
                $item->update(['disabled' => 0]);
            }
        });

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function destroyMedia(Item $item)
    {
        $data = DB::transaction(function () use ($item) {
            Storage::disk('public')->delete($item->image);
            $item->update(['image' => null]);
        });
        return redirect()->back();
    }

    public function destroy(Item $item)
    {
        $data = DB::transaction(function () use ($item) {
            Storage::disk('public')->delete($item->image);
            $item->delete();
        });
        return redirect()->back()->with('success', 'Successfully Deleted.');
    }
}
