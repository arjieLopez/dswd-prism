import jsPDF from "jspdf";
import "jspdf-autotable";
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
import * as XLSX from "xlsx";
window.XLSX = XLSX;

document.getElementById("export-xlsx")?.addEventListener("click", function () {
    exportDropdown.classList.add("hidden");
    var table = document.getElementById("purchase-requests-table");
    var tableClone = table.cloneNode(true);

    // Remove the last header cell (Action)
    var headerRow = tableClone.querySelector("thead tr");
    if (headerRow) {
        headerRow.removeChild(headerRow.lastElementChild);
    }
    // Remove the last cell from each body row
    tableClone.querySelectorAll("tbody tr").forEach(function (row) {
        row.removeChild(row.lastElementChild);
    });

    var wb = XLSX.utils.table_to_book(tableClone, {
        sheet: "Purchase Requests",
    });
    XLSX.writeFile(wb, "purchase_requests.xlsx");
});

// PDF export logic

document.getElementById("export-pdf")?.addEventListener("click", function () {
    exportDropdown.classList.add("hidden");
    var table = document.getElementById("purchase-requests-table");
    var doc = new jsPDF();

    // Add a title
    doc.setFontSize(16);
    doc.text("Purchase Requests Report", 14, 15);

    // Prepare table data (excluding last column)
    var head = [];
    var body = [];
    var headerCells = table.querySelectorAll("thead tr th");
    headerCells.forEach((th, i) => {
        if (i < headerCells.length - 1) head.push(th.innerText);
    });
    table.querySelectorAll("tbody tr").forEach((row) => {
        var rowData = [];
        row.querySelectorAll("td").forEach((td, i) => {
            if (i < row.cells.length - 1) rowData.push(td.innerText);
        });
        body.push(rowData);
    });

    doc.autoTable({
        head: [head],
        body: body,
        startY: 25,
        styles: { fontSize: 8 },
    });

    // Footer
    var pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(10);
        doc.text(
            "DSWD-PRISM • Generated: " + new Date().toLocaleDateString(),
            14,
            doc.internal.pageSize.height - 10
        );
        doc.text(
            "Page " + i + " of " + pageCount,
            doc.internal.pageSize.width - 40,
            doc.internal.pageSize.height - 10
        );
    }

    doc.save("purchase_requests.pdf");
});
