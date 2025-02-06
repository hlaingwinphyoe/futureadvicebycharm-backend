<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Http\Resources\ZodiacResource;
use App\Models\Package;
use App\Models\Zodiac;
use Illuminate\Http\Request;

class FrontController extends Controller
{
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
}
