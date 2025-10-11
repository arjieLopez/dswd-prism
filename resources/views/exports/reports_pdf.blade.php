<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSWD-PRISM Reports Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            color: #1a365d;
        }

        .header h2 {
            font-size: 12px;
            font-weight: normal;
            margin: 5px 0;
            color: #4a5568;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8px;
        }

        th,
        td {
            border: 1px solid #cbd5e0;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #edf2f7;
            font-weight: bold;
            font-size: 9px;
            color: #2d3748;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-approved {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .status-completed {
            background-color: #d6f5d6;
            color: #22543d;
        }

        .status-po-generated {
            background-color: #bee3f8;
            color: #2a4365;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }

        .summary {
            background-color: #f0fff4;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #9ae6b4;
            border-radius: 5px;
        }

        .summary h3 {
            font-size: 11px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #22543d;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
            padding: 2px 10px;
            font-size: 9px;
        }

        .summary-label {
            font-weight: bold;
            color: #2f855a;
        }

        .no-data {
            text-align: center;
            color: #718096;
            font-style: italic;
            padding: 30px;
            font-size: 11px;
        }

        .wrapped-text {
            word-wrap: break-word;
            word-break: break-word;
            max-width: 120px;
        }

        .filters-section {
            background-color: #f7fafc;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }

        .filters-section h3 {
            font-size: 11px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #2d3748;
        }

        .filter-item {
            font-size: 9px;
            margin: 3px 0;
            color: #4a5568;
        }

        .filter-label {
            font-weight: bold;
            color: #2d3748;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>DSWD-PRISM - Reports Export</h1>
        <h2>Purchase Requests and Purchase Orders Report</h2>
        <div style="font-size: 9px; color: #666;">
            Generated on: {{ $exported_at }}
        </div>
    </div>

    @if ($filters['search'] || $filters['status'] || $filters['date_from'] || $filters['date_to'])
        <div class="filters-section">
            <h3>Applied Filters:</h3>
            @if ($filters['search'])
                <div class="filter-item"><span class="filter-label">Search:</span> {{ $filters['search'] }}</div>
            @endif
            @if ($filters['status'] && $filters['status'] !== 'all')
                <div class="filter-item"><span class="filter-label">Status:</span>
                    {{ ucfirst(str_replace('_', ' ', $filters['status'])) }}</div>
            @endif
            @if ($filters['date_from'] || $filters['date_to'])
                <div class="filter-item">
                    <span class="filter-label">Date Range:</span>
                    @if ($filters['date_from'])
                        From {{ \Carbon\Carbon::parse($filters['date_from'])->format('F j, Y') }}
                    @endif
                    @if ($filters['date_to'])
                        To {{ \Carbon\Carbon::parse($filters['date_to'])->format('F j, Y') }}
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="summary">
        <h3>Export Summary</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Records:</div>
                <div class="summary-cell">{{ $reports->count() }}</div>
                <div class="summary-cell summary-label">PR Records:</div>
                <div class="summary-cell">{{ $reports->where('type', 'PR')->count() }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">PO Records:</div>
                <div class="summary-cell">{{ $reports->where('type', 'PO')->count() }}</div>
                <div class="summary-cell summary-label">Total Amount:</div>
                <div class="summary-cell">₱{{ number_format($reports->sum('amount'), 2) }}</div>
            </div>
        </div>
    </div>

    @if ($reports->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 10%;">Type</th>
                    <th style="width: 26%;">Document Number</th>
                    <th style="width: 30%;">Department</th>
                    <th style="width: 14%;">Status</th>
                    <th style="width: 12%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $index => $report)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $report->type }}</td>
                        <td class="wrapped-text">{{ $report->document_number }}</td>
                        <td class="wrapped-text">{{ $report->department }}</td>
                        <td class="text-center">
                            @php
                                $statusDisplay =
                                    $report->type === 'PO'
                                        ? 'PO Generated'
                                        : ucfirst(str_replace('_', ' ', $report->status));
                                $statusClass = match ($report->type === 'PO' ? 'po_generated' : $report->status) {
                                    'approved' => 'status-approved',
                                    'completed' => 'status-completed',
                                    'po_generated' => 'status-po-generated',
                                    default => '',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusDisplay }}</span>
                        </td>
                        <td class="text-right">₱{{ number_format($report->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            No reports found matching the specified criteria.
        </div>
    @endif

    <div class="footer">
        <div>Department of Social Welfare and Development</div>
        <div>PRISM - Procurement Information System</div>
        <div>{{ url('/') }}</div>
    </div>
</body>

</html>
