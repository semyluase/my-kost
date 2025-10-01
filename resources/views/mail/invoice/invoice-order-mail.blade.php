<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - Toko Sukses Makmur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 100px;
            height: auto;
        }

        .header h2 {
            margin: 10px 0 5px;
        }

        .info {
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }

        .receipt-details {
            font-size: 14px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .footer {
            font-size: 12px;
            text-align: center;
            color: #777;
            border-top: 1px dashed #ccc;
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
    <div class="email-container">
        <div class="header">
            <img src="{{ public_path('assets/image/Asset 1.png') }}" alt="Logo Toko">
            <h2>{{ $dataTransactionTransaction->user->location->name }}</h2>
        </div>

        <div class="info">
            {{ $dataTransaction->user->location->address }}<br>
            Telp.
            {{ Str::startWith('0', $dataTransaction->user->location->phone_number) ? Str::replaceFirst('0', '+62', $dataTransaction->user->location->phone_number) : (Str::startsWith($dataTransaction->user->location->phone_number, '62') ? '+' . $dataTransaction->user->location->phone_number : $dataTransaction->user->location->phone_number) }}<br>
            Tanggal: {{ Carbon::now('Asia/Jakarta')->isoFormat('DD/MM/YYYY - HH:mm') }}
        </div>

        <div class="receipt-details">
            <strong>Receipt No:</strong> {{ $dataTransactionTransaction->nobukti }}<br>
            <strong>Tanggal:</strong> {{ Carbon::now('Asia/Jakarta')->isoFormat('LL') }}<br>
            <strong>Waktu:</strong> {{ Carbon::now('Asia/Jakarta')->isoFormat('HH:mm') }} WIB
        </div>

        @php
            $total = 0;
        @endphp
        @if ($dataTransaction->is_order)
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataTransaction->details as $value)
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

        @if ($dataTransaction->is_laundry)
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Qty(kg)</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataTransaction->details as $value)
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

        @if ($dataTransaction->is_cleaning)
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Tanggal Request</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataTransaction->details as $value)
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

        @if ($dataTransaction->is_topup)
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Jumlah Topup</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataTransaction->details as $value)
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
            Total Pembayaran: {{ Number::currency($total, in: 'IDR', locale: 'id') }}
        </div>

        <div class="footer">
            Terima kasih atas kunjungan Anda!<br>
            Barang yang sudah dibeli tidak dapat dikembalikan.
        </div>
    </div>
