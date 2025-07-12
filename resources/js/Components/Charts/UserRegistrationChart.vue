<template>
    <div class="bg-transparent rounded shadow-lg border border-[#363638] p-6">
        <h3 class="text-lg font-bold mb-4">User Registration Trends</h3>
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
        type: "bar",
        data: {
            labels: props.data.map((item) => item.month),
            datasets: [
                {
                    label: "New Users",
                    data: props.data.map((item) => item.users),
                    backgroundColor: isDark ? "#3b82f6" : "#4B338C",
                    borderColor: isDark ? "#3b82f6" : "#4B338C",
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
                        text: "New Users",
                        color: isDark ? "#d1d5db" : "#374151",
                    },
                    ticks: {
                        color: isDark ? "#d1d5db" : "#374151",
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
