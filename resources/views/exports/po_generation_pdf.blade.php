<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Generation Export</title>
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

        .filters {
            background-color: #f7fafc;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }

        .filters h3 {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #2d3748;
        }

        .filter-item {
            margin-bottom: 3px;
            font-size: 9px;
        }

        .filter-label {
            font-weight: bold;
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

        .status-pending {
            background-color: #fef5e7;
            color: #744210;
        }

        .status-po-generated {
            background-color: #bee3f8;
            color: #2a4365;
        }

        .status-completed {
            background-color: #d6f5d6;
            color: #22543d;
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

        .overdue {
            color: #e53e3e;
            font-weight: bold;
        }

        .wrapped-text {
            word-wrap: break-word;
            word-break: break-word;
            max-width: 120px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>DSWD-PRISM - PO Generation Export</h1>
        <h2>Purchase Orders Generation Report</h2>
        <div style="font-size: 9px; color: #666;">
            Generated on: {{ now()->format('F j, Y g:i A') }}
        </div>
    </div>

    <div class="summary">
        <h3>Export Summary</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Records:</div>
                <div class="summary-cell">{{ $purchaseOrders->count() }}</div>
                <div class="summary-cell summary-label">Approved PRs:</div>
                <div class="summary-cell">{{ $purchaseRequests->where('status', 'approved')->count() }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Completed POs:</div>
                <div class="summary-cell">{{ $purchaseOrders->whereNotNull('completed_at')->count() }}</div>
                <div class="summary-cell summary-label">Pending POs:</div>
                <div class="summary-cell">{{ $purchaseOrders->whereNull('completed_at')->count() }}</div>
            </div>
        </div>
    </div>

    @if ($purchaseOrders->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 6%;">#</th>
                    <th style="width: 9%;">PR Number</th>
                    <th style="width: 13%;">Requestor</th>
                    <th style="width: 16%;">Purpose</th>
                    <th style="width: 11%;">Total Amount</th>
                    <th style="width: 9%;">Status</th>
                    <th style="width: 11%;">PO Number</th>
                    <th style="width: 13%;">Supplier</th>
                    <th style="width: 10%;">PO Generated</th>
                    <th style="width: 6%;">Items</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseOrders as $index => $po)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $po->pr_number }}</td>
                        <td class="wrapped-text">
                            {{ $po->first_name }}
                            @if ($po->middle_name)
                                {{ $po->middle_name }}
                            @endif
                            {{ $po->last_name }}
                        </td>
                        <td class="wrapped-text">{{ $po->purpose }}</td>
                        <td class="text-right">₱{{ number_format($po->total, 2) }}</td>
                        <td class="text-center">
                            <span class="status-badge status-po-generated">
                                PO Generated
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($pr->po_number)
                                {{ $pr->po_number }}
                            @else
                                <span style="color: #999;">N/A</span>
                            @endif
                        </td>
                        <td class="wrapped-text">
                            @if ($pr->supplier)
                                {{ $pr->supplier->supplier_name }}
                            @else
                                <span style="color: #999;">Not Assigned</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($po->generated_at)
                                {{ \Carbon\Carbon::parse($po->generated_at)->format('M j, Y') }}
                            @else
                                <span style="color: #999;">N/A</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $pr->items->count() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            No purchase requests found matching the specified criteria.
        </div>
    @endif

    <div class="footer">
        <div>Department of Social Welfare and Development</div>
        <div>PRISM - Procurement Information System</div>
        <div>{{ url('/') }}</div>
    </div>
</body>

</html>
