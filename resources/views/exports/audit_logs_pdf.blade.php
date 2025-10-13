<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSWD-PRISM Audit Logs Export</title>
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

        .role-admin {
            background-color: #fed7d7;
            color: #c53030;
        }

        .role-staff {
            background-color: #bee3f8;
            color: #2a4365;
        }

        .role-user {
            background-color: #c6f6d5;
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

        .wrapped-text {
            word-wrap: break-word;
            word-break: break-word;
            max-width: 120px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>DSWD-PRISM - Audit Logs Export</h1>
        <h2>System Activity Audit Trail</h2>
        <div style="font-size: 9px; color: #666;">
            Generated on: {{ $exportDate }}
        </div>
    </div>

    <div class="summary">
        <h3>Export Summary</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Records:</div>
                <div class="summary-cell">{{ $totalLogs }}</div>
                <div class="summary-cell summary-label">Admin Actions:</div>
                <div class="summary-cell">{{ $auditLogs->where('user_role', 'admin')->count() }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Staff Actions:</div>
                <div class="summary-cell">{{ $auditLogs->where('user_role', 'staff')->count() }}</div>
                <div class="summary-cell summary-label">User Actions:</div>
                <div class="summary-cell">{{ $auditLogs->where('user_role', 'user')->count() }}</div>
            </div>
        </div>
    </div>

    @if ($auditLogs->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 20%;">Timestamp</th>
                    <th style="width: 25%;">User</th>
                    <th style="width: 12%;">Role</th>
                    <th style="width: 35%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($auditLogs as $index => $log)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $log->created_at->format('F j, Y g:i A') }}</td>
                        <td class="wrapped-text">{{ $log->user_name }}</td>
                        <td class="text-center">
                            @php
                                $roleClass = match ($log->user_role) {
                                    'admin' => 'role-admin',
                                    'staff' => 'role-staff',
                                    'user' => 'role-user',
                                    default => 'role-user',
                                };
                            @endphp
                            <span class="status-badge {{ $roleClass }}">{{ ucfirst($log->user_role) }}</span>
                        </td>
                        <td class="wrapped-text">
                            {{ $log->description }}
                            @if ($log->pr_number)
                                <span style="color: #666;">({{ $log->pr_number }})</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            No audit logs found matching the specified criteria.
        </div>
    @endif

    <div class="footer">
        <div>Department of Social Welfare and Development</div>
        <div>PRISM - Procurement Information System</div>
        <div>{{ url('/') }}</div>
    </div>
</body>

</html>
