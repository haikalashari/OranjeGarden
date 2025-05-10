<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        @page {
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            color: #333;
            height: 100%;
            width: 100%;
            background-image: url('{{ public_path('storage/background_invoice/invoices.png') }}');
            background-repeat: no-repeat;
            background-position: center top;
            background-size: 100% auto;
        }

        .content {
            padding: 160px 60px 200px 60px;
            font-size: 14px;
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .offering-to {
            flex: 1;
        }

        .offering-to .label {
            font-weight: bold;
            color: #333;
        }

        .offering-to .name {
            font-weight: bold;
            color: #4CAF50;
            margin: 0;
        }

        .offering-to .address {
            margin: 0;
            color: #333;
        }

        .invoice-period {
            text-align: right;
            color: #4CAF50;
            font-weight: bold;
            font-size: 13px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .table th {
            background-color: #5b7743;
            color: #fff;
            padding: 8px;
            text-align: left;
        }

        .table td {
            padding: 8px;
            border-bottom: 1px dashed #999;
            background-color: rgba(255, 255, 255, 0.85);
        }

        .table td:last-child {
            text-align: right;
        }

        .total-box {
            background-color: #5b7743;
            color: #fff;
            font-weight: bold;
            text-align: right;
            padding: 10px;
            font-size: 14px;
        }

        .notes {
            margin-top: 30px;
        }

        .notes p {
            margin: 0 0 8px;
        }

        .notes ul {
            padding-left: 20px;
            margin: 0;
        }

        .notes ul li {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="content">

        <!-- Header with Offering To and Invoice Period -->
        <div class="header-flex">
            <!-- Offering To -->
            <div class="offering-to">
                <p><span class="label">Offering to:</span></p>
                <p class="name">{{ $order->customer->name }}</p>
                <p class="address">{{ $order->delivery_address }}</p>
            </div>

            <!-- Invoice Period -->
            <div class="invoice-period">
                <p>{{ \Carbon\Carbon::parse($order->start_date)->translatedFormat('d F Y') }}<br>
                   s/d {{ \Carbon\Carbon::parse($order->end_date)->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <!-- Invoice Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>PARTICULARS</th>
                    <th>RATE</th>
                    <th>QUANTITY</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedItems as $category => $data)
                    @if ($data['quantity'] > 0)
                        <tr>
                            <td>{{ ucfirst($category) }}</td>
                            <td>Rp {{ number_format($data['amount'] / $data['quantity'], 0, ',', '.') }}</td>
                            <td>{{ $data['quantity'] }}</td>
                            <td>Rp {{ number_format($data['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach
                @if ($installationFee > 0)
                    <tr>
                        <td>Biaya Instalasi</td>
                        <td>Rp {{ number_format($installationFee, 0, ',', '.') }}</td>
                        <td>1</td>
                        <td>Rp {{ number_format($installationFee, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Grand Total -->
        <div class="total-box">
            TOTAL &nbsp;&nbsp;&nbsp; Rp {{ number_format($grandTotal, 0, ',', '.') }}
        </div>

        <!-- Notes -->
        <div class="notes">
            <p><strong>KETERANGAN:</strong></p>
            <ul>
                @foreach (explode("\n", $invoiceNote) as $line)
                    @if (trim($line) !== '')
                        <li>{{ $line }}</li>
                    @endif
                @endforeach
            </ul>
        </div>

    </div>
</body>
</html>
