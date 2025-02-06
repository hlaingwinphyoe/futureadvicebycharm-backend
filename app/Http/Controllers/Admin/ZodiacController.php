<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zodiac;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ZodiacController extends Controller
{
    private $mediaSvc;
    public function __construct(MediaService $mediaSvc)
    {
        $this->mediaSvc = $mediaSvc;
    }

    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $zodiacs = Zodiac::query()
            ->filterOn()
            ->orderBy('id', 'asc')
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($zodiac) => [
                'id' => $zodiac->id,
                'name' => $zodiac->name,
                'period' => $zodiac->period,
                'image' => $zodiac->media,
                'desc' => $zodiac->desc
            ]);

        return Inertia::render('Admin/Zodiac/Index', [
            'zodiacs' => $zodiacs
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:zodiacs,name',
            'period' => 'required|string',
            'desc' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024'
        ]);

        try {
            DB::beginTransaction();
            $zodiac = Zodiac::create([
                'name' => $request->name,
                'period' => $request->period,
                'desc' => $request->desc,
            ]);

            if ($request->hasFile('image')) {
                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "zodiac",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $zodiac->update([
                    'image' => $url
                ]);
            }

            DB::commit();
            return redirect()->route('admin.zodiacs.index')->with('success', 'Successfully Created.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Zodiac $zodiac)
    {
        $request->validate([
            'name' => 'required|string|unique:zodiacs,name,' . $zodiac->id,
            'period' => 'required|string',
            'desc' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024'
        ]);

        try {
            DB::beginTransaction();
            $zodiac->update([
                'name' => $request->name,
                'period' => $request->period,
                'desc' => $request->desc,
            ]);

            if ($request->hasFile('image')) {
                // Delete the old image
                if ($zodiac->image !== null) {
                    Storage::disk('public')->delete($zodiac->image);
                }
                $mediaFormdata = [
                    'media' => $request->file('image'),
                    'type' => "zodiac",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $zodiac->update([
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
}
