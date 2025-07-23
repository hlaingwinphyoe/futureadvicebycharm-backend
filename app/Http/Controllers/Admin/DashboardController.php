<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Feedback;
use App\Models\Package;
use App\Models\Post;
use App\Models\User;
use App\Models\PostView;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get analytics data
        $analytics = $this->getAnalyticsData();

        return Inertia::render('Dashboard', [
            'analytics' => $analytics
        ]);
    }

    private function getAnalyticsData()
    {
        $now = now();
        $startOfMonth = $now->startOfMonth();
        $startOfYear = $now->startOfYear();

        // Optimized: Get all counts in single queries with proper conditions
        $totalPackages = Package::count();
        $totalPosts = Post::count();

        // Optimized: Get revenue data with single query
        $revenueData = Appointment::selectRaw('
            SUM(CASE WHEN is_paid = 1 THEN total_price ELSE 0 END) as total_revenue,
            SUM(CASE WHEN created_at >= ? THEN total_price ELSE 0 END) as monthly_revenue,
            SUM(CASE WHEN created_at >= ? THEN total_price ELSE 0 END) as yearly_revenue
        ', [$startOfMonth, $startOfYear])
            ->where('is_paid', true)
            ->first();

        // Optimized: Get appointment counts with single query
        $appointmentData = Appointment::selectRaw('
            COUNT(*) as total_appointments,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as monthly_appointments,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as yearly_appointments
        ', [$startOfMonth, $startOfYear])
            ->first();

        // Optimized: Get user counts with single query
        $userData = User::selectRaw('
            COUNT(*) as total_users,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as monthly_users
        ', [$startOfMonth])
            ->first();

        // Optimized: Get trends data with single query for appointments
        $appointmentTrends = Appointment::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            COUNT(*) as count,
            SUM(total_price) as revenue
        ')
            ->where('created_at', '>=', $now->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->take(6)
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'appointments' => (int) $item->count,
                    'revenue' => (float) $item->revenue
                ];
            });

        // Optimized: Get user registration trends
        $userRegistrationTrends = User::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            COUNT(*) as count
        ')
            ->where('created_at', '>=', $now->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->take(6)
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'users' => (int) $item->count
                ];
            });

        // Optimized: Get post view trends with single query
        $postViewTrends = PostView::selectRaw('
            DATE_FORMAT(post_views.created_at, "%Y-%m") as month,
            COUNT(*) as view_count
        ')
            ->join('posts', 'post_views.post_id', '=', 'posts.id')
            ->where('post_views.created_at', '>=', $now->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->take(6)
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'view_count' => (int) $item->view_count
                ];
            });

        // Optimized: Get revenue by package with eager loading
        $revenueByPackage = DB::table('appointments')
            ->join('appointment_packages', 'appointments.id', '=', 'appointment_packages.appointment_id')
            ->join('packages', 'appointment_packages.package_id', '=', 'packages.id')
            ->selectRaw('
                packages.name as package_name,
                SUM(appointment_packages.price) as total_revenue,
                COUNT(*) as booking_count
            ')
            ->where('appointments.is_paid', true)
            ->groupBy('packages.id', 'packages.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'package_name' => $item->package_name,
                    'total_revenue' => (float) $item->total_revenue,
                    'booking_count' => (int) $item->booking_count
                ];
            });

        // Optimized: Get popular posts with single query
        $popularPosts = Post::selectRaw('
            posts.title,
            posts.poster,
            COUNT(post_views.id) as view_count,
            posts.created_at
        ')
            ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
            ->groupBy('posts.id', 'posts.title', 'posts.poster', 'posts.created_at')
            ->orderByDesc('view_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'title' => $item->title,
                    'poster' => $item->poster ? asset('storage/' . $item->poster) : null,
                    'view_count' => (int) $item->view_count,
                    'created_at' => $item->created_at->format('M d, Y')
                ];
            });

        // Optimized: Get appointment status distribution
        $appointmentStatusDistribution = DB::table('appointments')
            ->join('statuses', 'appointments.status_id', '=', 'statuses.id')
            ->selectRaw('
                statuses.name as status,
                COUNT(*) as count
            ')
            ->groupBy('statuses.id', 'statuses.name')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status,
                    'count' => (int) $item->count
                ];
            });

        // Optimized: Get recent appointments with eager loading
        $recentAppointments = Appointment::with(['user', 'status'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'appointment_no' => $appointment->appointment_no,
                    'customer_name' => $appointment->user ? $appointment->user->name : 'N/A',
                    'status' => $appointment->status ? $appointment->status->name : 'N/A',
                    'total_price' => (float) $appointment->total_price,
                    'is_paid' => (bool) $appointment->is_paid,
                    'created_at' => $appointment->created_at->format('M d, Y')
                ];
            });

        return [
            'overview' => [
                'total_users' => (int) $userData->total_users,
                'total_appointments' => (int) $appointmentData->total_appointments,
                'total_packages' => (int) $totalPackages,
                'total_posts' => (int) $totalPosts,
                'total_revenue' => (float) $revenueData->total_revenue,
                'monthly_appointments' => (int) $appointmentData->monthly_appointments,
                'monthly_revenue' => (float) $revenueData->monthly_revenue,
                'monthly_users' => (int) $userData->monthly_users,
                'yearly_appointments' => (int) $appointmentData->yearly_appointments,
                'yearly_revenue' => (float) $revenueData->yearly_revenue,
            ],
            'trends' => [
                'appointment_trends' => $appointmentTrends,
                'user_registration_trends' => $userRegistrationTrends,
            ],
            'analytics' => [
                'revenue_by_package' => $revenueByPackage,
                'popular_posts' => $popularPosts,
                'post_view_trends' => $postViewTrends,
                'appointment_status_distribution' => $appointmentStatusDistribution,
            ],
            'recent_activity' => [
                'recent_appointments' => $recentAppointments,
            ]
        ];
    }

    public function feedback()
    {
        $pageSize = request('page_size') ?: 10;
        $feedback = Feedback::query()
            ->filterOn()
            ->paginate($pageSize)
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

    public function feedbackDestroy($id)
    {
        $feedback = Feedback::findOrFail($id);

        $feedback->delete();

        return redirect()->back()->with('success', 'Successfully Deleted.');
    }
}
