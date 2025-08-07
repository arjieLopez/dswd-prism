<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase Request</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }
        }

        body {
            padding: 40px;
        }

        .pr-title {
            font-size: 1rem;
            font-weight: bold;
            text-align: center;
            margin: 1rem;
        }

        .justify-between {
            display: flex;
            justify-content: space-between;
        }

        .pr-table,
        .pr-table th,
        .pr-table td {
            border: 1px solid #000;
        }

        .pr-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .pr-table th,
        .pr-table td {
            padding: 4px 8px;
        }

        .signature-label {
            font-size: 0.9rem;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 80%;
            margin: 0 auto 0.25rem auto;
        }
    </style>
</head>

<body>
    <div class="max-w-3xl mx-auto bg-white p-8 rounded shadow">
        <table class="pr-table">
            <!-- Header Title Row -->
            <tr>
                <td colspan="6">
                    <div class="pr-title">PURCHASE REQUEST (PR)</div>
                    <div class="justify-between">
                        <span><strong>Entity Name:</strong> {{ $purchaseRequest->entity_name }}</span>
                        <span><strong>Fund Cluster:</strong> {{ $purchaseRequest->fund_cluster }}</span>
                    </div>
                </td>
            </tr>
            <!-- Office/Section / PR No / Responsibility Center Code / Date -->
            <tr>
                <td colspan="2"><strong>Office/Section:</strong> {{ $purchaseRequest->office_section }}</td>
                <td colspan="2">
                    <strong>PR No:</strong> {{ $purchaseRequest->pr_number }}<br>
                    <strong>Responsibility Center Code:</strong> ____________________
                </td>
                <td colspan="2"><strong>Date:</strong>
                    {{ \Carbon\Carbon::parse($purchaseRequest->date)->format('F d, Y') }}</td>
            </tr>
            <!-- Table Headers for Items -->
            <tr>
                <th style="width: 10%;">Stock/ Property No.</th>
                <th style="width: 5%;">Unit</th>
                <th>Description</th>
                <th style="width: 5%;">Quantity</th>
                <th style="width: 15%;">Unit Cost</th>
                <th style="width: 15%;">Total Cost</th>
            </tr>
            <!-- Item Rows -->
            @php
                $descriptions = preg_split('/\r\n|\r|\n/', $purchaseRequest->item_description);
            @endphp
            @foreach ($descriptions as $i => $desc)
                <tr>
                    <td class="text-center"></td>
                    <td class="text-center">{{ $i == 0 ? $purchaseRequest->unit : '' }}</td>
                    <td>{{ $desc }}</td>
                    <td class="text-center">{{ $i == 0 ? $purchaseRequest->quantity : '' }}</td>
                    <td class="text-right">{{ $i == 0 ? '₱' . number_format($purchaseRequest->unit_cost, 2) : '' }}
                    </td>
                    <td class="text-right">{{ $i == 0 ? '₱' . number_format($purchaseRequest->total_cost, 2) : '' }}
                    </td>
                </tr>
            @endforeach
            <!-- Purpose Row -->
            <tr>
                <td colspan="3" style="vertical-align: top; text-align: left;"><strong>Purpose:</strong>
                    {{ $purchaseRequest->purpose }}</td>
                <td colspan="3" style="vertical-align: top;"> This is to certify that items/supplies indicated in the
                    PR are included in the
                    PPMP/APP and the Technical Specifications/ Terms of Reference and Scope of Works are reviewed and
                    approved by the Procurement section Head. <br><br><br><br></td>
            </tr>
            <!-- Signature Row -->
            <tr>
                <td colspan="2">
                    <div class="signature-label" style="text-align: end;">
                        <br>
                        Signature: <br><br>
                        Printed Name: <br>
                        Designation:
                    </div>
                </td>
                <td style="text-align: center;">
                    <div style="padding-top: 3rem;"></div>
                    <div class="signature-line"></div>
                    <div class="signature-label">Requested
                        by:<br>{{ $purchaseRequest->requested_by_name ?? '_____________________' }}</div>
                </td>
                <td colspan="3" style="text-align: center;">
                    <div style="padding-top: 3rem;"></div>
                    <div class="signature-line"></div>
                    <div class="signature-label">Approved by:<br>_______________________</div>
                </td>
            </tr>
        </table>

        <button class="no-print mt-8 px-4 py-2 bg-blue-600 text-white rounded" onclick="window.print()">Print</button>
        <a href="{{ url()->previous() }}" class="no-print ml-2 px-4 py-2 bg-gray-400 text-white rounded">Back</a>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
