<template>
    <div class="bg-transparent rounded shadow-lg border border-[#363638] p-6">
        <h3 class="text-lg font-bold mb-4">Revenue Trends</h3>
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

    chart = new Chart(ctx, {
        type: "line",
        data: {
            labels: props.data.map((item) => item.month),
            datasets: [
                {
                    label: "Revenue",
                    data: props.data.map((item) => item.revenue),
                    backgroundColor: isDark
                        ? "rgba(245, 158, 11, 0.1)"
                        : "rgba(245, 158, 11, 0.1)",
                    borderColor: isDark ? "#f59e0b" : "#f59e0b",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: isDark ? "#f59e0b" : "#f59e0b",
                    pointBorderColor: isDark ? "#1f2937" : "#ffffff",
                    pointBorderWidth: 2,
                    pointRadius: 4,
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
                        text: "Month",
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
