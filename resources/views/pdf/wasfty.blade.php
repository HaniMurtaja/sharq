<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Label</title>
    <style>
        body {
            font-size: 14px;
            color: #2d467f;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .label-container {
            border: 1px solid black;
            padding: 20px;
            max-width: 600px;
            margin: 20px auto;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .barcode-container {
            text-align: right;
        }

        .barcode-container svg {
            width: 100px;
            height: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table td {
            padding: 8px;
            border: 1px solid black;
        }

        .fw-bold {
            font-weight: bold;
        }

        .fs-3 {
            font-size: 18px;
            font-weight: bold;
        }

        .fs-6 {
            font-size: 14px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .footer img {
            width: 100px;
        }

        .footer p {
            margin: 0;
            font-size: 12px;
        }

        .right-align {
            text-align: right;
        }

        .no-border {
            border: none;
        }
    </style>
</head>
<body>
<div class="label-container">
    <!-- Header Section -->
    <div class="header-section">
        <h1 class="fs-3"><strong>Package:</strong> 1/1</h1>
        <div class="barcode-container">
            <!-- Barcode will be dynamically generated -->
            {!! App\Helpers\BarcodeHelper::generateBarcodePNG($order->id) !!}
        </div>
    </div>

    <!-- Main Table -->
    <table>
        <tr>
            <td colspan="2"><strong>Reference #:</strong></td>
            <td colspan="2">{{ $order->client_order_id_string }}</td>
        </tr>
        <tr>
            <td colspan="2" class="fw-bold">Weight:</td>
            <td colspan="2" class="right-align">{{ $order->weight ?? '1kg' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="fw-bold">Created Date:</td>
            <td colspan="2" class="right-align">{{ $order->created_at->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="fw-bold">Estimated Delivery Date:</td>
            <td colspan="2" class="right-align">{{ $order->deliver_at ? $order->deliver_at->format('F d, Y') : 'N/A' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="fw-bold">Drop Off Date:</td>
            <td colspan="2" class="right-align">{{ $order->drop_off_date ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="fw-bold">Service Type:</td>
            <td colspan="2" class="right-align">{{ $order->service_type ?? 'Express Delivery' }}</td>
        </tr>
        <tr>
            <td colspan="4" class="fw-bold">From / Sender</td>
        </tr>
        <tr>
            <td colspan="2">{{ $order->ingr_shop_name }}</td>
            <td colspan="2">{{ $order->ingr_branch_name }}</td>
        </tr>
        <tr>
            <td colspan="4"><strong>Address:</strong> {{ $order->ingr_branch_address ?? '--' }}</td>
        </tr>
        <tr>
            <td colspan="4"><strong>Phone:</strong> {{ $order->ingr_branch_phone }}</td>
        </tr>
        <tr>
            <td colspan="4" class="fw-bold">To / Recipient</td>
        </tr>
        <tr>
            <td colspan="4">{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <td colspan="4"><strong>Address:</strong> {{ $order->customer_address }}</td>
        </tr>
        <tr>
            <td colspan="4"><strong>Phone:</strong> {{ $order->customer_phone }}</td>
        </tr>
        <tr>
            <td class="fw-bold">City:</td>
            <td>{{ $order->city ?? '--' }}</td>
            <td class="fw-bold">Area:</td>
            <td>{{ $order->area ?? '--' }}</td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div>
            <p class="fs-6">Fulfilled by: Alshrouq Delivery App</p>
            <br>
            <img src="{{ public_path('pdf/logo-2.png') }}" alt="Company Logo" >
        </div>
        <div class="barcode-container">
            {!! App\Helpers\BarcodeHelper::generateBarcodePNG($order->id) !!}
        </div>
    </div>
</div>
</body>
</html>
