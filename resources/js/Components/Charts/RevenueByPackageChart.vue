<template>
    <div class="bg-transparent rounded shadow-lg border border-[#363638] p-6">
        <h3 class="text-lg font-bold mb-4">Revenue by Package</h3>
        <div class="relative" style="height: 300px">
            <canvas ref="chartRef"></canvas>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
});

const chartRef = ref(null);
let chart = null;

const createChart = () => {
    if (chart) {
        chart.destroy();
    }

    const ctx = chartRef.value.getContext("2d");
    const savedTheme = localStorage.getItem("theme");
    const isDark = savedTheme ? JSON.parse(savedTheme) === "dark" : false;

    const colors = [
        isDark ? "#3b82f6" : "#4B338C",
        isDark ? "#10b981" : "#10B981",
        isDark ? "#f59e0b" : "#F59E0B",
        isDark ? "#ef4444" : "#EF4444",
        isDark ? "#8b5cf6" : "#8B5CF6",
    ];

    chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: props.data.map((item) => item.package_name),
            datasets: [
                {
                    label: "Revenue (MMK)",
                    data: props.data.map((item) => item.total_revenue),
                    backgroundColor: colors.slice(0, props.data.length),
                    borderColor: colors.slice(0, props.data.length),
                    borderWidth: 1,
                    borderRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: "Package",
                        color: isDark ? "#d1d5db" : "#374151",
                    },
                    ticks: {
                        color: isDark ? "#d1d5db" : "#374151",
                    },
                    grid: {
                        color: isDark ? "#374151" : "#e5e7eb",
                    },
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: "Revenue (MMK)",
                        color: isDark ? "#d1d5db" : "#374151",
                    },
                    ticks: {
                        color: isDark ? "#d1d5db" : "#374151",
                        callback: function (value) {
                            return new Intl.NumberFormat("en-US", {
                                style: "currency",
                                currency: "MMK",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            }).format(value);
                        },
                    },
                    grid: {
                        color: isDark ? "#374151" : "#e5e7eb",
                    },
                    beginAtZero: true,
                },
            },
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        color: isDark ? "#d1d5db" : "#374151",
                    },
                },
                title: {
                    display: false,
                },
            },
        },
    });
};

onMounted(() => {
    if (props.data && props.data.length > 0) {
        createChart();
    }
});

watch(
    () => props.data,
    () => {
        if (props.data && props.data.length > 0) {
            createChart();
        }
    },
    { deep: true }
);
</script>
