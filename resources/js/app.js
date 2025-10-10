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
        const completedPR = window.userCompletedChartData || [];

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
                    {
                        label: "Completed PRs",
                        data: completedPR,
                        borderColor: "rgba(37, 99, 235, 1)",
                        backgroundColor: "rgba(37, 99, 235, 1)",
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

// import * as XLSX from "xlsx";
// window.XLSX = XLSX;

// document.getElementById("export-btn").addEventListener("click", function () {
//     var table = document.getElementById("purchase-requests-table");
//     // Clone the table
//     var tableClone = table.cloneNode(true);

//     // Remove the last header cell (Action)
//     var headerRow = tableClone.querySelector("thead tr");
//     if (headerRow) {
//         headerRow.removeChild(headerRow.lastElementChild);
//     }

//     // Remove the last cell from each body row
//     tableClone.querySelectorAll("tbody tr").forEach(function (row) {
//         row.removeChild(row.lastElementChild);
//     });

//     // Export the modified table
//     var wb = XLSX.utils.table_to_book(tableClone, {
//         sheet: "Purchase Requests",
//     });
//     XLSX.writeFile(wb, "purchase_requests.xlsx");
// });

const exportBtn = document.getElementById("export-btn");
const exportDropdown = document.getElementById("export-dropdown");
const exportDropdownContainer = document.getElementById(
    "export-dropdown-container"
);

if (exportBtn && exportDropdown) {
    exportBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        exportDropdown.classList.toggle("hidden");
    });

    // Hide dropdown when clicking outside
    document.addEventListener("click", function (e) {
        if (!exportDropdownContainer.contains(e.target)) {
            exportDropdown.classList.add("hidden");
        }
    });
}

// XLSX export logic
document.getElementById("export-xlsx")?.addEventListener("click", function () {
    exportDropdown.classList.add("hidden");

    // Get current URL parameters to preserve filters
    const urlParams = new URLSearchParams(window.location.search);
    const formData = new FormData();

    // Add all current filter parameters to the form
    if (urlParams.get("search"))
        formData.append("search", urlParams.get("search"));
    if (urlParams.get("status"))
        formData.append("status", urlParams.get("status"));
    if (urlParams.get("date_from"))
        formData.append("date_from", urlParams.get("date_from"));
    if (urlParams.get("date_to"))
        formData.append("date_to", urlParams.get("date_to"));

    // Add CSRF token
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (csrfToken) formData.append("_token", csrfToken);

    // Determine which export route to use based on current page
    const currentPath = window.location.pathname;
    let exportUrl = "/purchase-requests/export/xlsx";
    let filenamePrefix = "purchase_requests";

    if (currentPath.includes("pr-review") || currentPath.includes("staff")) {
        exportUrl = "/staff/pr-review/export/xlsx";
        filenamePrefix = "pr_review";
    }

    // Create and submit form
    fetch(exportUrl, {
        method: "POST",
        body: formData,
    })
        .then((response) => response.blob())
        .then((blob) => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download =
                filenamePrefix +
                "_" +
                new Date().toISOString().slice(0, 19).replace(/:/g, "-") +
                ".csv";
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch((error) => {
            console.error("Export failed:", error);
            alert("Export failed. Please try again.");
        });
});

// PDF export logic
document.getElementById("export-pdf")?.addEventListener("click", function () {
    exportDropdown.classList.add("hidden");

    // Get current URL parameters to preserve filters
    const urlParams = new URLSearchParams(window.location.search);
    const formData = new FormData();

    // Add all current filter parameters to the form
    if (urlParams.get("search"))
        formData.append("search", urlParams.get("search"));
    if (urlParams.get("status"))
        formData.append("status", urlParams.get("status"));
    if (urlParams.get("date_from"))
        formData.append("date_from", urlParams.get("date_from"));
    if (urlParams.get("date_to"))
        formData.append("date_to", urlParams.get("date_to"));

    // Add CSRF token
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (csrfToken) formData.append("_token", csrfToken);

    // Determine which export route to use based on current page
    const currentPath = window.location.pathname;
    let exportUrl = "/purchase-requests/export/pdf";
    let filenamePrefix = "purchase_requests";

    if (currentPath.includes("pr-review") || currentPath.includes("staff")) {
        exportUrl = "/staff/pr-review/export/pdf";
        filenamePrefix = "pr_review";
    }

    // Create and submit form
    fetch(exportUrl, {
        method: "POST",
        body: formData,
    })
        .then((response) => response.blob())
        .then((blob) => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download =
                filenamePrefix +
                "_" +
                new Date().toISOString().slice(0, 19).replace(/:/g, "-") +
                ".pdf";
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch((error) => {
            console.error("Export failed:", error);
            alert("Export failed. Please try again.");
        });
});
