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
            <td colspan="2">{{ $purchaseOrder->supplier->supplier_name ?? '' }}</td>
            <td class="bold">P.O. No. :</td>
            <td colspan="2">{{ $purchaseOrder->po_number }}</td>
        </tr>
        <tr>
            <td class="bold">Address :</td>
            <td colspan="2">{{ $purchaseOrder->supplier->address ?? '' }}</td>
            <td class="bold">Date :</td>
            <td colspan="2">
                {{ $purchaseOrder->generated_at ? $purchaseOrder->generated_at->format('Y-m-d') : '' }}</td>
        </tr>
        <tr>
            <td class="bold">TIN :</td>
            <td colspan="2">{{ $purchaseOrder->supplier->tin ?? '' }}</td>
            <td class="bold">Mode of Procurement:</td>
            <td colspan="2">{{ $purchaseOrder->mode_of_procurement }}</td>
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
            <td>{{ $purchaseRequest->delivery_address ?? ($purchaseRequest->place_of_delivery ?? '') }}</td>
            <td class="bold">Delivery Term :</td>
            <td colspan="2">{{ $purchaseOrder->delivery_term }}</td>
        </tr>
        <tr>
            <td colspan="2" class="bold">Date of Delivery :</td>
            <td>{{ $purchaseOrder->date_of_delivery ? \Carbon\Carbon::parse($purchaseOrder->date_of_delivery)->format('F j, Y') : '' }}
            </td>
            <td class="bold">Payment Term :</td>
            <td colspan="2">{{ $purchaseOrder->payment_term }}</td>
        </tr>
        <tr>
            <th width="10%">Stock/Property No.</th>
            <th width="8%">Unit</th>
            <th width="40%">Description</th>
            <th width="8%" class="center">Quantity</th>
            <th width="12%" class="right">Unit Cost</th>
            <th width="12%" class="right">Amount</th>
        </tr>
        @if ($purchaseRequest->items && $purchaseRequest->items->count() > 0)
            @foreach ($purchaseRequest->items as $item)
                @php
                    $descriptions = preg_split('/\r\n|\r|\n/', $item->item_description);
                @endphp
                @foreach ($descriptions as $i => $desc)
                    <tr>
                        <td class="center"></td>
                        <td class="center">{{ $i == 0 ? $item->unit : '' }}</td>
                        <td>{{ $desc }}</td>
                        <td class="center">{{ $i == 0 ? $item->quantity : '' }}</td>
                        <td class="right">{{ $i == 0 ? '₱' . number_format($item->unit_cost, 2) : '' }}</td>
                        <td class="right">{{ $i == 0 ? '₱' . number_format($item->total_cost, 2) : '' }}</td>
                    </tr>
                @endforeach
            @endforeach
        @else
            <tr>
                <td colspan="6" class="center">No items found</td>
            </tr>
        @endif
        <tr>
            <td colspan="6" class="center">***** NOTHING FOLLOWS *****</td>
        </tr>
        <tr>
            <td colspan="2" class="center bold">(Total Amount in Words)</td>
            <td colspan="3">
                @php
                    function numberToWords($number)
                    {
                        $ones = [
                            0 => '',
                            1 => 'ONE',
                            2 => 'TWO',
                            3 => 'THREE',
                            4 => 'FOUR',
                            5 => 'FIVE',
                            6 => 'SIX',
                            7 => 'SEVEN',
                            8 => 'EIGHT',
                            9 => 'NINE',
                            10 => 'TEN',
                            11 => 'ELEVEN',
                            12 => 'TWELVE',
                            13 => 'THIRTEEN',
                            14 => 'FOURTEEN',
                            15 => 'FIFTEEN',
                            16 => 'SIXTEEN',
                            17 => 'SEVENTEEN',
                            18 => 'EIGHTEEN',
                            19 => 'NINETEEN',
                        ];

                        $tens = [
                            0 => '',
                            2 => 'TWENTY',
                            3 => 'THIRTY',
                            4 => 'FORTY',
                            5 => 'FIFTY',
                            6 => 'SIXTY',
                            7 => 'SEVENTY',
                            8 => 'EIGHTY',
                            9 => 'NINETY',
                        ];

                        if ($number < 20) {
                            return $ones[$number];
                        } elseif ($number < 100) {
                            return $tens[intval($number / 10)] . ($number % 10 != 0 ? ' ' . $ones[$number % 10] : '');
                        } elseif ($number < 1000) {
                            return $ones[intval($number / 100)] .
                                ' HUNDRED' .
                                ($number % 100 != 0 ? ' ' . numberToWords($number % 100) : '');
                        } elseif ($number < 1000000) {
                            return numberToWords(intval($number / 1000)) .
                                ' THOUSAND' .
                                ($number % 1000 != 0 ? ' ' . numberToWords($number % 1000) : '');
                        } elseif ($number < 1000000000) {
                            return numberToWords(intval($number / 1000000)) .
                                ' MILLION' .
                                ($number % 1000000 != 0 ? ' ' . numberToWords($number % 1000000) : '');
                        }
                        return 'NUMBER TOO LARGE';
                    }

                    $total = $purchaseRequest->total ?? 0;
                    $pesos = floor($total);
                    $centavos = round(($total - $pesos) * 100);

                    $totalInWords = '';
                    if ($pesos > 0) {
                        $totalInWords = numberToWords($pesos) . ' PESOS';
                    }
                    if ($centavos > 0) {
                        $totalInWords .= ($pesos > 0 ? ' AND ' : '') . numberToWords($centavos) . ' CENTAVOS';
                    }
                    if ($pesos == 0 && $centavos == 0) {
                        $totalInWords = 'ZERO PESOS';
                    }
                    $totalInWords .= ' ONLY';
                @endphp
                {{ $totalInWords }}
            </td>
            <td class="right bold">₱{{ number_format($purchaseRequest->total, 2) }}</td>
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
