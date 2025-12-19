<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Package;
use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getAnalytics(): array
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        return [
            'overview' => $this->getOverviewStats($startOfMonth, $startOfYear),
            'trends' => [
                'appointment_trends' => $this->getAppointmentTrends(),
                'user_registration_trends' => $this->getUserRegistrationTrends(),
            ],
            'analytics' => [
                'revenue_by_package' => $this->getRevenueByPackage(),
                'popular_posts' => $this->getPopularPosts(),
                'post_view_trends' => $this->getPostViewTrends(),
                'appointment_status_distribution' => $this->getAppointmentStatusDistribution(),
            ],
            'recent_activity' => [
                'recent_appointments' => $this->getRecentAppointments(),
            ]
        ];
    }

    private function getOverviewStats($startOfMonth, $startOfYear): array
    {
        // Aggregate Revenue
        $revenue = Appointment::query()
            ->where('is_paid', true)
            ->selectRaw('
                SUM(total_price) as total,
                SUM(CASE WHEN created_at >= ? THEN total_price ELSE 0 END) as monthly,
                SUM(CASE WHEN created_at >= ? THEN total_price ELSE 0 END) as yearly
            ', [$startOfMonth, $startOfYear])
            ->first();

        // Aggregate Appointments
        $appointments = Appointment::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as monthly,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as yearly
            ', [$startOfMonth, $startOfYear])
            ->first();

        // Aggregate Users
        $users = User::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as monthly
            ', [$startOfMonth])
            ->first();

        return [
            'total_users' => (int) $users->total,
            'monthly_users' => (int) $users->monthly,
            'total_appointments' => (int) $appointments->total,
            'monthly_appointments' => (int) $appointments->monthly,
            'yearly_appointments' => (int) $appointments->yearly,
            'total_revenue' => (float) $revenue->total,
            'monthly_revenue' => (float) $revenue->monthly,
            'yearly_revenue' => (float) $revenue->yearly,
            'total_packages' => Package::count(),
            'total_posts' => Post::count(),
        ];
    }

    private function getAppointmentTrends()
    {
        return Appointment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(total_price) as revenue')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'month' => $item->month,
                'appointments' => (int) $item->count,
                'revenue' => (float) $item->revenue
            ]);
    }

    private function getUserRegistrationTrends()
    {
        return User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'month' => $item->month,
                'users' => (int) $item->count
            ]);
    }

    private function getPostViewTrends()
    {
        return PostView::selectRaw('DATE_FORMAT(post_views.created_at, "%Y-%m") as month, COUNT(*) as view_count')
            ->join('posts', 'post_views.post_id', '=', 'posts.id')
            ->where('post_views.created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'month' => $item->month,
                'view_count' => (int) $item->view_count
            ]);
    }

    private function getRevenueByPackage()
    {
        return DB::table('appointments')
            ->join('appointment_packages', 'appointments.id', '=', 'appointment_packages.appointment_id')
            ->join('packages', 'appointment_packages.package_id', '=', 'packages.id')
            ->selectRaw('packages.name as package_name, SUM(appointment_packages.price) as total_revenue, COUNT(*) as booking_count')
            ->where('appointments.is_paid', true)
            ->groupBy('packages.id', 'packages.name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'package_name' => $item->package_name,
                'total_revenue' => (float) $item->total_revenue,
                'booking_count' => (int) $item->booking_count
            ]);
    }

    private function getPopularPosts()
    {
        return Post::selectRaw('posts.title, posts.poster, COUNT(post_views.id) as view_count, posts.created_at')
            ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
            ->groupBy('posts.id', 'posts.title', 'posts.poster', 'posts.created_at')
            ->orderByDesc('view_count')
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'title' => $item->title,
                'poster' => $item->poster ? asset('storage/' . $item->poster) : null,
                'view_count' => (int) $item->view_count,
                'created_at' => $item->created_at->format('M d, Y')
            ]);
    }

    private function getAppointmentStatusDistribution()
    {
        return DB::table('appointments')
            ->join('statuses', 'appointments.status_id', '=', 'statuses.id')
            ->selectRaw('statuses.name as status, COUNT(*) as count')
            ->groupBy('statuses.id', 'statuses.name')
            ->get()
            ->map(fn($item) => [
                'status' => $item->status,
                'count' => (int) $item->count
            ]);
    }

    private function getRecentAppointments()
    {
        return Appointment::with(['user:id,name', 'status:id,name']) // Optimized Selects
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($appointment) => [
                'id' => $appointment->id,
                'appointment_no' => $appointment->appointment_no,
                'customer_name' => $appointment->user->name ?? 'N/A',
                'status' => $appointment->status->name ?? 'N/A',
                'total_price' => (float) $appointment->total_price,
                'is_paid' => (bool) $appointment->is_paid,
                'created_at' => $appointment->created_at->format('M d, Y')
            ]);
    }
}
