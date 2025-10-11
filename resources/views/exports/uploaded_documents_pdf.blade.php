<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Uploaded Documents PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>Uploaded Documents</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>PR Number</th>
                <th>File Name</th>
                <th>File Type</th>
                <th>File Size</th>
                <th>Upload Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($documents as $index => $doc)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $doc->pr_number }}</td>
                    <td>{{ $doc->original_filename }}</td>
                    <td>{{ strtoupper($doc->file_type) }}</td>
                    <td>{{ $doc->file_size_formatted }}</td>
                    <td>{{ $doc->created_at->format('M d, Y H:i') }}</td>
                    <td>{{ $doc->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
