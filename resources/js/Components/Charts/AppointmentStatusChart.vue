<template>
    <div class="bg-transparent rounded shadow-lg border border-[#363638] p-6">
        <h3 class="text-lg font-bold mb-4">Appointment Status Distribution</h3>
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

    // Define semantic colors for appointment statuses
    const getStatusColor = (status) => {
        const statusLower = status.toLowerCase();

        if (statusLower.includes("pending")) {
            return isDark ? "#f59e0b" : "#F59E0B";
        } else if (
            statusLower.includes("approved") ||
            statusLower.includes("completed")
        ) {
            return isDark ? "#10b981" : "#10B981";
        } else if (
            statusLower.includes("incomplete") ||
            statusLower.includes("cancelled")
        ) {
            return isDark ? "#ef4444" : "#EF4444";
        } else {
            // Default colors for other statuses
            return isDark ? "#3b82f6" : "#4B338C";
        }
    };

    const colors = props.data.map((item) => getStatusColor(item.status));

    chart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: props.data.map((item) => item.status),
            datasets: [
                {
                    data: props.data.map((item) => item.count),
                    backgroundColor: colors,
                    borderColor: isDark ? "#1f2937" : "#ffffff",
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        color: isDark ? "#d1d5db" : "#374151",
                        padding: 20,
                        usePointStyle: true,
                    },
                },
                title: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const total = context.dataset.data.reduce(
                                (a, b) => a + b,
                                0
                            );
                            const percentage = (
                                (context.parsed / total) *
                                100
                            ).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        },
                    },
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
