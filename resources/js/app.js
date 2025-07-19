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
