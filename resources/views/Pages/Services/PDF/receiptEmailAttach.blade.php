<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .receipt {
            max-width: 350px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border: 1px dashed #333;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo img {
            max-width: 100px;
            height: auto;
        }

        .receipt h2 {
            text-align: center;
            margin: 5px 0;
        }

        .receipt .info {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .receipt .info .receipt-number {
            font-weight: bold;
            margin-top: 5px;
        }

        .receipt table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .receipt table th,
        .receipt table td {
            padding: 5px;
            text-align: left;
        }

        .receipt table th {
            border-bottom: 1px solid #333;
        }

        .receipt .total {
            margin-top: 15px;
            font-weight: bold;
            text-align: right;
        }

        .receipt .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
            border-top: 1px dashed #333;
            padding-top: 10px;
        }
    </style>
</head>
@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Number;
    use Illuminate\Support\Str;
@endphp

<body>
    <div class="receipt">
        <div class="logo">
            <img src="{{ $image }}" alt="Trifena Residence">
        </div>
        <h2>{{ $data->user->location->name }}</h2>
        <div class="info">
            {{ $data->user->location->address }}<br>
            {{ Str::startWith('0', $data->user->location->phone_number) ? Str::replaceFirst('0', '+62', $data->user->location->phone_number) : (Str::startsWith($data->user->location->phone_number, '62') ? '+' . $data->user->location->phone_number : $data->user->location->phone_number) }}<br>
            Tanggal: {{ Carbon::now('Asia/Jakarta')->isoFormat('DD/MM/YYYY - HH:mm') }}
            <div class="receipt-number">Receipt No: {{ $data->nobukti }}</div>
        </div>
        @php
            $total = 0;
        @endphp
        @if ($data->is_order)
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->details as $value)
                        <tr>
                            <td>{{ $value->foodSnack->name }}</td>
                            <td>{{ $value->qty }}</td>
                            <td>{{ Number::currency($value->harga_jual * $value->qty, in: 'IDR', locale: 'id') }}</td>
                            @php
                                $total += $value->harga_jual * $value->qty;
                            @endphp
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if ($data->is_laundry)
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Qty(kg)</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->details as $value)
                        <tr>
                            <td>{{ $value->categorylaundry->name }}</td>
                            <td>{{ $value->categorylaundry->weight }}</td>
                            <td>{{ Number::currency($value->harga_laundry, in: 'IDR', locale: 'id') }}</td>
                            @php
                                $total += $value->harga_laundry;
                            @endphp
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if ($data->is_cleaning)
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Tanggal Request</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->details as $value)
                        <tr>
                            <td>{{ $value->categoryCleaning->description }}</td>
                            <td>{{ Carbon::parse($value->tgl_request_cleaning)->isoFormat('DD/MM/YYYY HH:mm') }}</td>
                            <td>{{ Number::currency($value->harga_cleaning, in: 'IDR', locale: 'id') }}</td>
                            @php
                                $total += $value->harga_cleaning;
                            @endphp
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if ($data->is_topup)
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Jumlah Topup</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->details as $value)
                        <tr>
                            <td>{{ Number::currency($value->qty, in: 'IDR', locale: 'id') }}</td>
                            <td>{{ Carbon::parse($value->created_at)->isoFormat('DD/MM/YYYY HH:mm') }}</td>
                            <td>{{ Number::currency($value->qty, in: 'IDR', locale: 'id') }}</td>
                            @php
                                $total += $value->qty;
                            @endphp
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="total">
            Total: {{ Number::currency($total, in: 'IDR', locale: 'id') }}
        </div>

        <div class="footer">
            Terima kasih atas kunjungan Anda!<br>
            Barang yang sudah dibeli tidak dapat dikembalikan.
        </div>
    </div>
</body>
