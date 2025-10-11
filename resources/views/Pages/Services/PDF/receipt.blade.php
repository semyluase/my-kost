<!DOCTYPE html>
<html>

<head>
    <title>Receipt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            margin-top: 10px;
        }

        p {
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            width: 750px;
            /* Adjust as needed for typical receipt width */
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 15px;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .item-table th,
        .item-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .item-table th {
            background-color: #f9f9f9;
        }

        .total-section {
            text-align: right;
            margin-top: 10px;
        }

        .total-section div {
            margin-bottom: 5px;
        }

        .thank-you {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>

<body>
    @php
        use Illuminate\Support\Carbon;
    @endphp
    <div class="receipt-container">
        <div class="header">
            <h2>Pekerjaan</h2>
        </div>

        <p>Tanggal: {{ Carbon::now('Asia/Jakarta')->isoFormat('DD MMMM YYYY') }}</p>
        <p>Time: {{ Carbon::now('Asia/Jakarta')->isoFormat('HH:mm') }}</p>

        <table class="item-table">
            <thead>
                <tr>
                    <th>No Bukti</th>
                    <th>No Kamar</th>
                    <th>Kategori</th>
                    <th>Item</th>
                    <th>Check</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction as $value)
                    @if ($value->is_order)
                        @foreach ($value->details as $item)
                            <tr>
                                <td>{{ $value->nobukti }}</td>
                                <td>{{ $value->room ? $value->room->number_room : '-' }}</td>
                                <td>Order</td>
                                <td>
                                    @if ($item->foodSnack)
                                        {{ $item->foodSnack->name }} {{ $item->qty }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><input type="checkbox" name="" id=""></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ $value->nobukti }}</td>
                            <td>{{ $value->room ? $value->room->number_room : '-' }}</td>
                            <td>
                                @if ($value->is_laundry)
                                    Laundry
                                @elseif ($value->is_cleaning)
                                    Pembersihan
                                @endif
                            </td>
                            <td>
                                @if (collect($value->details)->count() > 0)
                                    @if ($value->is_cleaning)
                                        @if ($value->details->first()->categoryCleaning)
                                            <div>{{ $value->details->first()->categoryCleaning->description }}</div>
                                            <div>
                                                {{ Carbon::parse($value->details->first()->tgl_request_cleaning)->isoFormat('HH:mm') }}
                                            </div>
                                        @else
                                            -
                                        @endif
                                    @elseif ($value->is_laundry)
                                        {{ $value->details->first()->categoryLaundry ? $value->details->first()->categoryLaundry->name : '-' }}
                                    @endif
                                @endif
                            </td>
                            <td><input type="checkbox" name="" id=""></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
