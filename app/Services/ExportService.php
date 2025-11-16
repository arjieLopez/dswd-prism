<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class ExportService
{
    /**
     * Export data to CSV
     *
     * @param array $headers
     * @param array $rows
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function exportToCSV(array $headers, array $rows, string $filename)
    {
        $csvContent = [];
        $csvContent[] = $headers;

        foreach ($rows as $row) {
            $csvContent[] = $row;
        }

        // Create CSV file
        $handle = fopen('php://temp', 'r+');
        foreach ($csvContent as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csvData = stream_get_contents($handle);
        fclose($handle);

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export data to PDF
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $orientation
     * @param string $paperSize
     * @return \Illuminate\Http\Response
     */
    public function exportToPDF(string $view, array $data, string $filename, string $orientation = 'portrait', string $paperSize = 'a4')
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($paperSize, $orientation);

        return $pdf->download($filename);
    }

    /**
     * Generate filename with timestamp
     *
     * @param string $prefix
     * @param string $extension
     * @return string
     */
    public function generateFilename(string $prefix, string $extension = 'csv'): string
    {
        return $prefix . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
    }

    /**
     * Prepare filter summary for exports
     *
     * @param array $filters
     * @return array
     */
    public function prepareFilterSummary(array $filters): array
    {
        $summary = [];

        if (!empty($filters['search'])) {
            $summary[] = 'Search: "' . $filters['search'] . '"';
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $summary[] = 'Status: ' . ucfirst($filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $summary[] = 'From: ' . date('M j, Y', strtotime($filters['date_from']));
        }

        if (!empty($filters['date_to'])) {
            $summary[] = 'To: ' . date('M j, Y', strtotime($filters['date_to']));
        }

        if (!empty($filters['role']) && $filters['role'] !== 'all') {
            $summary[] = 'Role: ' . ucfirst($filters['role']);
        }

        return $summary;
    }
}
