<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSWD-PRISM Users Export</title>
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

        .status-active {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .status-inactive {
            background-color: #fed7d7;
            color: #c53030;
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
        <h1>DSWD-PRISM - Users Export</h1>
        <h2>User Management Report</h2>
        <div style="font-size: 9px; color: #666;">
            Generated on: {{ $exportDate }}
        </div>
    </div>

    <div class="summary">
        <h3>Export Summary</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Users:</div>
                <div class="summary-cell">{{ $totalUsers }}</div>
                <div class="summary-cell summary-label">Active Users:</div>
                <div class="summary-cell">{{ $users->where('email_verified_at', '!=', null)->count() }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Inactive Users:</div>
                <div class="summary-cell">{{ $users->where('email_verified_at', null)->count() }}</div>
                <div class="summary-cell summary-label">Export Date:</div>
                <div class="summary-cell">{{ $exportDate }}</div>
            </div>
        </div>
    </div>

    @if ($users->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 6%;">#</th>
                    <th style="width: 20%;">Name</th>
                    <th style="width: 18%;">Email Address</th>
                    <th style="width: 10%;">Role</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 14%;">Designation</th>
                    <th style="width: 11%;">Employee ID</th>
                    <th style="width: 11%;">Office</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $index => $user)
                    @php
                        $fullName =
                            $user->first_name .
                            ($user->middle_name ? ' ' . $user->middle_name : '') .
                            ' ' .
                            $user->last_name;
                        $status = $user->email_verified_at ? 'Active' : 'Inactive';
                        $statusClass = $user->email_verified_at ? 'status-active' : 'status-inactive';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="wrapped-text">{{ $fullName }}</td>
                        <td class="wrapped-text">{{ $user->email }}</td>
                        <td class="text-center">{{ ucfirst($user->role) }}</td>
                        <td class="text-center">
                            <span class="status-badge {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td class="wrapped-text">{{ $user->designation ?? '-' }}</td>
                        <td class="text-center">{{ $user->employee_id ?? '-' }}</td>
                        <td class="wrapped-text">{{ $user->office ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            No users found matching the specified criteria.
        </div>
    @endif

    <div class="footer">
        <div>Department of Social Welfare and Development</div>
        <div>PRISM - Procurement Information System</div>
        <div>{{ url('/') }}</div>
    </div>
</body>

</html>
