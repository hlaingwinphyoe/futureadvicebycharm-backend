<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Services\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'analytics' => $this->dashboardService->getAnalytics()
        ]);
    }

    public function feedback(Request $request): Response
    {
        $feedback = Feedback::query()
            ->filterOn() // Assuming this scope exists in your Trait/Model
            ->latest()
            ->paginate($request->input('page_size', 10))
            ->withQueryString()
            ->through(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'message' => $item->message,
                'rating' => $item->rating,
                'type' => $item->type,
                'created_at' => $item->created_at->diffForHumans(),
            ]);

        return Inertia::render('Admin/Feedback/Index', [
            'feedback' => $feedback
        ]);
    }

    public function feedbackDestroy(int $id): RedirectResponse
    {
        Feedback::findOrFail($id)->delete();

        return back()->with('success', 'Successfully Deleted.');
    }
}
