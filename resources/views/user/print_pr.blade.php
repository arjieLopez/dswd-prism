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

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .signature-label {
            font-size: 12px;
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
                <td colspan="2"><strong>Office/Section:</strong> {{ $purchaseRequest->office->name }}</td>
                <td colspan="2">
                    <strong>PR No:</strong> {{ $purchaseRequest->pr_number }}<br>
                    <strong>Responsibility Center Code:</strong> {{ $purchaseRequest->responsibility_center_code }}
                </td>
                <td colspan="2"><strong>Date:</strong>
                    {{ \Carbon\Carbon::parse($purchaseRequest->date)->format('F d, Y') }}</td>
            </tr>
            <!-- Table Headers for Items -->
            <tr>
                <th style="width: 10%;">Stock/ Property No.</th>
                <th style="width: 5%;">Unit</th>
                <th>Description</th>
                <th style="width: 5%; text-align: center;">Quantity</th>
                <th style="width: 15%; text-align: right;">Unit Cost</th>
                <th style="width: 15%; text-align: right;">Total Cost</th>
            </tr>
            <!-- Item Rows -->
            @if ($purchaseRequest->items && $purchaseRequest->items->count() > 0)
                @foreach ($purchaseRequest->items as $item)
                    @php
                        $descriptions = preg_split('/\r\n|\r|\n/', $item->item_description);
                    @endphp
                    @foreach ($descriptions as $i => $desc)
                        <tr>
                            <td class="text-center"></td>
                            <td class="text-center">{{ $i == 0 ? $item->unit : '' }}</td>
                            <td>{{ $desc }}</td>
                            <td class="text-center">{{ $i == 0 ? $item->quantity : '' }}</td>
                            <td class="text-right">{{ $i == 0 ? '₱' . number_format($item->unit_cost, 2) : '' }}</td>
                            <td class="text-right">{{ $i == 0 ? '₱' . number_format($item->total_cost, 2) : '' }}</td>
                        </tr>
                    @endforeach
                @endforeach
                <!-- Total Row -->
                <tr>
                    <td colspan="5" class="text-left"><strong>TOTAL AMOUNT:</strong></td>
                    <td class="text-right"><strong>₱{{ number_format($purchaseRequest->total, 2) }}</strong></td>
                </tr>
            @else
                <tr>
                    <td colspan="6" class="text-center">No items found</td>
                </tr>
            @endif
            <!-- Purpose Row -->
            <tr>
                <td colspan="3" style="vertical-align: top; text-align: left;"><strong>Purpose:</strong>
                    {{ $purchaseRequest->purpose }}</td>
                <td colspan="3" style="vertical-align: top; padding: 8px; font-size: 12px;">
                    This is to certify that items/supplies indicated in the
                    PR are included in the
                    PPMP/APP and the Technical Specifications/ Terms of Reference and Scope of Works are reviewed and
                    approved by the Procurement section Head.

                    @if ($primaryApprover || $secondaryApprover)
                        <div style="margin-top: 20px; display: flex; justify-content: space-between; gap: 30px;">
                            @if ($primaryApprover)
                                <!-- Primary Approver -->
                                <div style="flex: 1; text-align: center;">
                                    <div style="padding-top: 2rem;"></div>
                                    <div style="margin-bottom: 0.25rem;">
                                        {{ $primaryApprover->first_name }}
                                        @if ($primaryApprover->middle_name)
                                            {{ strtoupper(substr($primaryApprover->middle_name, 0, 1)) }}.
                                        @endif
                                        {{ $primaryApprover->last_name }}
                                    </div>
                                    <div style="font-size: 0.75rem;">
                                        {{ $primaryApprover->designation ? $primaryApprover->designation->name : '' }}
                                    </div>
                                </div>
                            @endif

                            @if ($secondaryApprover)
                                <!-- Secondary Approver -->
                                <div style="flex: 1; text-align: center;">
                                    <div style="padding-top: 2rem;"></div>
                                    <div style="margin-bottom: 0.25rem;">
                                        {{ $secondaryApprover->first_name }}
                                        @if ($secondaryApprover->middle_name)
                                            {{ strtoupper(substr($secondaryApprover->middle_name, 0, 1)) }}.
                                        @endif
                                        {{ $secondaryApprover->last_name }}
                                    </div>
                                    <div style="font-size: 0.75rem;">
                                        {{ $secondaryApprover->designation ? $secondaryApprover->designation->name : '' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </td>
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
                    <div class="signature-label">Requested by:</div>
                    <div style="padding-top: 2rem;"></div>
                    <div
                        style="text-align: center; margin-bottom: 0.5rem; border-bottom: 1px solid #000; width: 80%; margin: 0 auto;">
                        @if ($purchaseRequest->user)
                            {{ $purchaseRequest->user->first_name }}
                            @if ($purchaseRequest->user->middle_name)
                                {{ strtoupper(substr($purchaseRequest->user->middle_name, 0, 1)) }}.
                            @endif
                            {{ $purchaseRequest->user->last_name }}
                        @else
                            _____________________
                        @endif
                    </div>
                    <div class="signature-label">
                        {{ $purchaseRequest->user ? $purchaseRequest->user->designation : '_____________________' }}
                    </div>
                </td>
                <td colspan="3" style="text-align: center;">
                    <div class="signature-label">Approved by:</div>
                    <div style="padding-top: 2rem;"></div>
                    <div
                        style="text-align: center; margin-bottom: 0.5rem; border-bottom: 1px solid #000; width: 80%; margin: 0 auto;">
                        @php
                            $approvedStatus = \App\Models\Status::where('name', 'approved')->first();
                            $approval = $purchaseRequest->approvals->where('status_id', $approvedStatus?->id)->first();
                            $approver = $approval?->approver;
                        @endphp
                        @if ($approver)
                            {{ $approver->first_name }}
                            @if ($approver->middle_name)
                                {{ strtoupper(substr($approver->middle_name, 0, 1)) }}.
                            @endif
                            {{ $approver->last_name }}
                        @else
                            _____________________
                        @endif
                    </div>
                    <div class="signature-label">
                        {{ $approver?->designation ?? '_____________________' }}
                    </div>
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
