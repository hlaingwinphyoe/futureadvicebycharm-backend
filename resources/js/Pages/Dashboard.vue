<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import StatsCard from "@/Components/StatsCard.vue";
import RevenueChart from "@/Components/Charts/RevenueChart.vue";
import UserRegistrationChart from "@/Components/Charts/UserRegistrationChart.vue";
import PostViewChart from "@/Components/Charts/PostViewChart.vue";
import RevenueByPackageChart from "@/Components/Charts/RevenueByPackageChart.vue";
import AppointmentStatusChart from "@/Components/Charts/AppointmentStatusChart.vue";
import RecentActivity from "@/Components/RecentActivity.vue";
import PopularPosts from "@/Components/PopularPosts.vue";
import { usePage } from "@inertiajs/vue3";

const { analytics } = usePage().props;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="p-4 container mx-auto overflow-x-auto">
            <div class="mb-3">
                <h4 class="text-lg font-bold">Dashboard</h4>
                <p class="text-sm">Key metrics and performance insights</p>
            </div>
            <div class="p-1 index-content">
                <!-- Stats Cards -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6"
                >
                    <StatsCard
                        title="Total Users"
                        :value="analytics.overview.total_users"
                        icon="UsersIcon"
                        format="number"
                    />
                    <StatsCard
                        title="Total Appointments"
                        :value="analytics.overview.total_appointments"
                        icon="CalendarIcon"
                        format="number"
                    />
                    <StatsCard
                        title="Total Revenue"
                        :value="analytics.overview.total_revenue"
                        icon="CurrencyDollarIcon"
                        format="currency"
                    />
                    <StatsCard
                        title="Total Posts"
                        :value="analytics.overview.total_posts"
                        icon="DocumentTextIcon"
                        format="number"
                    />
                </div>

                <!-- Monthly Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                    <StatsCard
                        title="Monthly Appointments"
                        :value="analytics.overview.monthly_appointments"
                        icon="CalendarIcon"
                        format="number"
                    />
                    <StatsCard
                        title="Monthly Revenue"
                        :value="analytics.overview.monthly_revenue"
                        icon="CurrencyDollarIcon"
                        format="currency"
                    />
                    <StatsCard
                        title="Monthly New Users"
                        :value="analytics.overview.monthly_users"
                        icon="UserIcon"
                        format="number"
                    />
                </div>

                <!-- User Registration and Post View Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
                    <RecentActivity
                        :appointments="
                            analytics.recent_activity.recent_appointments
                        "
                    />
                    <PopularPosts :posts="analytics.analytics.popular_posts" />
                </div>

                <!-- Revenue and Appointment Status Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
                    <RevenueChart :data="analytics.trends.appointment_trends" />
                    <UserRegistrationChart
                        :data="analytics.trends.user_registration_trends"
                    />
                </div>

                <!-- Revenue by Package Chart -->
                <div class="mb-6">
                    <RevenueByPackageChart
                        :data="analytics.analytics.revenue_by_package"
                    />
                </div>

                <!-- Recent Activity and Popular Posts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <PostViewChart
                        :data="analytics.analytics.post_view_trends"
                    />
                    <AppointmentStatusChart
                        :data="
                            analytics.analytics.appointment_status_distribution
                        "
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
