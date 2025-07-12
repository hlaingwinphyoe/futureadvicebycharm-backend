<template>
    <div
        class="bg-transparent rounded shadow-lg border border-[#363638] p-6"
    >
        <h3 class="text-lg font-semibold mb-4">Recent Appointments</h3>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div
                v-for="appointment in appointments"
                :key="appointment.id"
                class="flex items-center justify-between p-4"
            >
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 bg-primary-100 dark:bg-primary-500 rounded-full flex items-center justify-center"
                        >
                            <UserIcon
                                class="w-4 h-4 text-primary-600 dark:text-primary-100"
                            />
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium">
                            {{ appointment.customer_name }}
                        </p>
                        <p class="text-xs">#{{ appointment.appointment_no }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-medium">
                            {{ formatCurrency(appointment.total_price) }}
                        </p>
                        <p class="text-xs">
                            {{ appointment.created_at }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="getStatusClass(appointment.status)"
                        >
                            {{ appointment.status }}
                        </span>
                        <span
                            v-if="appointment.is_paid"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                        >
                            Paid
                        </span>
                    </div>
                </div>
            </div>

            <div v-if="appointments.length === 0" class="text-center py-8">
                <UserIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium">No recent appointments</h3>
                <p class="mt-1 text-sm">New appointments will appear here.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { UserIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    appointments: {
        type: Array,
        required: true,
    },
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "MMK",
    }).format(amount);
};

const getStatusClass = (status) => {
    const statusClasses = {
        Pending:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        Confirmed:
            "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        Completed:
            "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        Cancelled: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        default:
            "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200",
    };

    return statusClasses[status] || statusClasses.default;
};
</script>
