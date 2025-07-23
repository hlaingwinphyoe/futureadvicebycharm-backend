<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\ZodiacResource;
use App\Models\Bank;
use App\Models\Category;
use App\Models\Feedback;
use App\Models\Package;
use App\Models\Status;
use App\Models\SystemInfo;
use App\Models\Zodiac;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    public function getBanks()
    {
        $banks = Bank::notDisabled()->get();

        $banks = BankResource::collection($banks);

        return $this->sendResponse($banks, 'Success!');
    }

    public function getGenders()
    {
        $genders = Status::isType('sex')->orderBy('id', 'asc')->get();

        return $this->sendResponse($genders, 'Success!');
    }

    public function getWeekdays()
    {
        $weekdays = Status::isType('weekdays')->orderBy('id', 'asc')->get();

        return $this->sendResponse($weekdays, 'Success!');
    }
    public function getZodiacs()
    {
        $zodiacs = Zodiac::all();

        $zodiacs = ZodiacResource::collection($zodiacs);

        return $this->sendResponse($zodiacs, 'Success!');
    }

    public function getPackages(Request $request)
    {
        try {
            $page_size = $request->page_size ? $request->page_size : 12;
            $packages = Package::query()
                ->with('astrologer')
                ->filterOn()
                ->published()
                ->orderBy('id', 'desc')
                ->paginate($page_size)
                ->withQueryString();

            $packages = PackageResource::collection($packages)->response()->getData(true);

            return $this->sendResponse($packages, 'Success!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getPackagesAll(Request $request)
    {
        try {
            $packages = Package::with(['astrologer', 'currency'])
                ->published()
                ->orderBy('name')
                ->get();

            $packages = PackageResource::collection($packages)->response()->getData(true);

            return $this->sendResponse($packages, 'Success!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getPackage($id)
    {
        try {
            $package = Package::with('astrologer')->findOrFail($id);

            $package = new PackageResource($package);
            return $this->sendResponse($package, "Success!");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getCategories()
    {
        $categories = Category::orderBy('name', 'asc')->get();

        $categories = CategoryResource::collection($categories);

        return $this->sendResponse($categories, 'Success!');
    }

    public function sendFeedback(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|max:255",
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'type' => "required|string|in:feedback,suggestion,service,reading,website"
        ]);

        $feedback = DB::transaction(function () use ($request) {
            Feedback::create([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
                'rating' => $request->rating,
                'type' => $request->type,
            ]);
        });

        return $this->sendResponse($feedback, 'Success!');
    }

    public function getInfo()
    {
        $system_info = SystemInfo::with('phones')->first();

        return $this->sendResponse($system_info, 'Success!');
    }
}
