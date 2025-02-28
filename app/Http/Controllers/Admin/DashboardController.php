<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard');
    }

    public function feedback()
    {
        $pageSize = request('page_size') ?: 10;
        $feedback = ContactMessage::query()
            ->filterOn()
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'email' => $item->email,
                'message' => $item->message,
                'created_at' => $item->created_at->diffForHumans(),
            ]);

        return Inertia::render('Admin/Feedback/Index', [
            'feedback' => $feedback
        ]);
    }

    public function feedbackDestroy($id)
    {
        $feedback = ContactMessage::findOrFail($id);

        $feedback->delete();

        return redirect()->back()->with('success', 'Successfully Deleted.');
    }
}
