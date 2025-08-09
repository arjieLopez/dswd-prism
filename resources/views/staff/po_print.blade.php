<!DOCTYPE html>
<html>

<head>
    <title>Purchase Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #222;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #222;
            padding: 4px 6px;
        }

        .no-border {
            border: none !important;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        /* .no-print {
            display: none;
        } */

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td colspan="6" class="center bold no-border" style="font-size:18px;">PURCHASE ORDER</td>
        </tr>
        <tr>
            <td colspan="6" class="center bold no-border" style="font-size:18px;">{{ $purchaseRequest->entity_name }}
            </td>
        </tr>
        <tr>
            <td colspan="6" class="center no-border">Entity Name</td>
        </tr>
        <tr>
            <td class="bold">Supplier :</td>
            <td colspan="2">{{ $purchaseRequest->supplier->supplier_name ?? '' }}</td>
            <td class="bold">P.O. No. :</td>
            <td colspan="2">{{ $purchaseRequest->po_number }}</td>
        </tr>
        <tr>
            <td class="bold">Address :</td>
            <td colspan="2">{{ $purchaseRequest->supplier->address ?? '' }}</td>
            <td class="bold">Date :</td>
            <td colspan="2">
                {{ $purchaseRequest->po_generated_at ? $purchaseRequest->po_generated_at->format('Y-m-d') : '' }}</td>
        </tr>
        <tr>
            <td class="bold">TIN :</td>
            <td colspan="2">{{ $purchaseRequest->supplier->tin ?? '' }}</td>
            <td class="bold">Mode of Procurement:</td>
            <td colspan="2">{{ $purchaseRequest->mode_of_procurement }}</td>
        </tr>
        <tr>
            <td colspan="6" class="bold">Sir/Madame:</td>
        </tr>
        <tr>
            <td colspan="6" class="bold">Please furnish this Office the following articles subject to the terms and
                conditions contained herein:</td>
        </tr>
        <tr>
            <td colspan="2" class="bold">Place of Delivery :</td>
            <td>{{ $purchaseRequest->place_of_delivery }}</td>
            <td class="bold">Delivery Term :</td>
            <td colspan="2">{{ $purchaseRequest->delivery_term }}</td>
        </tr>
        <tr>
            <td colspan="2" class="bold">Date of Delivery :</td>
            <td>{{ $purchaseRequest->date_of_delivery }}</td>
            <td class="bold">Payment Term :</td>
            <td colspan="2">{{ $purchaseRequest->payment_term }}</td>
        </tr>
        <tr>
            <th width="10%">Stock/Property No.</th>
            <th width="8%">Unit</th>
            <th width="40%">Description</th>
            <th width="8%">Quantity</th>
            <th width="12%">Unit Cost</th>
            <th width="12%">Amount</th>
        </tr>
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
        <tr>
            <td colspan="6" class="center">***** NOTHING FOLLOWS *****</td>
        </tr>
        <tr>
            <td colspan="2" class="center bold">(Total Amount in Words)</td>
            <td colspan="3"></td>
            <td class="right bold">₱{{ number_format($purchaseRequest->total_cost, 2) }}</td>
        </tr>
        <tr>
            <td colspan="6" style="border-top:1px solid #222;">
                {{-- <div style="margin-top:10px;">
                    <strong>(Total Amount in Words):</strong>
                    {{ number_format($purchaseRequest->total_cost, 2) }} PESOS ONLY
                </div>
                <div style="margin-top:10px;"> --}}
                <strong>In case of failure to make the full delivery within the time specified above, a penalty of
                    one-tenth (1/10) of one percent for every day of delay shall be imposed.</strong>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="vertical-align:top; padding-top:30px;">
                <strong>Conforme:</strong><br><br>
                ___________________________<br>
                Signature over Printed Name<br>
                Date: _____________________
            </td>
            <td colspan="3" style="text-align:right; vertical-align:top; padding-top:30px;">
                <strong>Very truly yours,</strong><br><br>
                ___________________________<br>
                Authorized Official
            </td>
        </tr>
    </table>
    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()" style="padding:6px 16px;">Print</button>
        <a href="{{ url()->previous() }}" style="margin-left: 12px;">Back</a>
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
