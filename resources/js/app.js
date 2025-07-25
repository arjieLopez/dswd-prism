import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

import "flowbite";
import Chart from "chart.js/auto";

document.addEventListener("DOMContentLoaded", () => {
    const el = document.getElementById("bar-chart");
    if (el && window.chartLabels && window.chartPR && window.chartPO) {
        new Chart(el, {
            type: "bar",
            data: {
                labels: window.chartLabels,
                datasets: [
                    {
                        label: "Total PR",
                        data: window.chartPR,
                        backgroundColor: "rgba(71, 74, 255, 0.9)",
                    },
                    {
                        label: "Total PO",
                        data: window.chartPO,
                        backgroundColor: "rgba(77, 206, 65, 0.9)",
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true },
                },
                plugins: {
                    legend: {
                        position: "bottom",
                    },
                },
            },
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const chartCanvas = document.getElementById("prLineChart");
    if (chartCanvas) {
        const labels = window.prChartLabels || [];
        const approvePR = window.userApproveChartData || [];
        const pendingPR = window.userPendingChartData || [];
        const rejectPR = window.userRejectChartData || [];

        new Chart(chartCanvas, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Approved PRs",
                        data: approvePR,
                        borderColor: "rgba(67, 160, 71, 1)",
                        backgroundColor: "rgba(67, 160, 71, 1)",
                        // fill: true,
                        // tension: 0.4,
                    },
                    {
                        label: "Pending PRs",
                        data: pendingPR,
                        borderColor: "rgba(251, 192, 45, 1)",
                        backgroundColor: "rgba(251, 192, 45, 1)",
                        // fill: true,
                        // tension: 0.4,
                    },
                    {
                        label: "Rejected PRs",
                        data: rejectPR,
                        borderColor: "rgba(211, 47, 47, 1)",
                        backgroundColor: "rgba(211, 47, 47, 1)",
                        // fill: true,
                        // tension: 0.4,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: "bottom" },
                },
                scales: {
                    y: { beginAtZero: true },
                },
            },
        });
    }
});
