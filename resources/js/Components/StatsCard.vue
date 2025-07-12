<template>
    <div
        class="bg-transparent rounded shadow-lg border border-[#363638] p-6 hover:shadow-xl transition-all duration-300"
    >
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div
                    class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg"
                    :class="iconBgClass"
                >
                    <component
                        :is="icon"
                        class="w-6 h-6"
                        :class="iconColorClass"
                    />
                </div>
            </div>
            <div class="ml-4 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium truncate">
                        {{ title }}
                        <span
                            v-if="format === 'currency'"
                            class="text-xs text-gray-500 dark:text-gray-400 ml-1"
                            >(MMK)</span
                        >
                    </dt>
                    <dd class="text-2xl font-bold">
                        {{ formatValue(value) }}
                    </dd>
                </dl>
            </div>
        </div>
        <div v-if="change" class="mt-4">
            <div class="flex items-center">
                <component
                    :is="changeIcon"
                    class="w-4 h-4 mr-1"
                    :class="changeColorClass"
                />
                <span class="text-sm font-semibold" :class="changeColorClass">
                    {{ change }}%
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">
                    from last month
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import {
    UsersIcon,
    CalendarIcon,
    CurrencyDollarIcon,
    DocumentTextIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [Number, String],
        required: true,
    },
    icon: {
        type: String,
        default: "UsersIcon",
    },
    change: {
        type: Number,
        default: null,
    },
    format: {
        type: String,
        default: "number", // number, currency, percentage
    },
});

const iconComponents = {
    UsersIcon,
    CalendarIcon,
    CurrencyDollarIcon,
    DocumentTextIcon,
};

const icon = computed(() => iconComponents[props.icon] || UsersIcon);

const iconBgClass = computed(() => {
    switch (props.icon) {
        case "UsersIcon":
            return "bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700";
        case "CalendarIcon":
            return "bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-700";
        case "CurrencyDollarIcon":
            return "bg-gradient-to-br from-amber-500 to-amber-600 dark:from-amber-600 dark:to-amber-700";
        case "DocumentTextIcon":
            return "bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700";
        default:
            return "bg-gradient-to-br from-gray-500 to-gray-600 dark:from-gray-600 dark:to-gray-700";
    }
});

const iconColorClass = computed(() => {
    return "text-white";
});

const changeIcon = computed(() => {
    return props.change >= 0 ? ArrowTrendingUpIcon : ArrowTrendingDownIcon;
});

const changeColorClass = computed(() => {
    return props.change >= 0
        ? "text-emerald-600 dark:text-emerald-400"
        : "text-red-600 dark:text-red-400";
});

const formatValue = (value) => {
    if (props.format === "currency") {
        return new Intl.NumberFormat("en-US").format(value);
    } else if (props.format === "percentage") {
        return `${value}%`;
    } else {
        return new Intl.NumberFormat("en-US").format(value);
    }
};
</script>
