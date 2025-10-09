<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $data->no_invoice }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" />
</head>

<body>
    @php
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Number;
    @endphp
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-6">
                <div class="h3 text-dark">{{ $data->member->user->location->name }}</div>
                <p class="h4 text-dark">{{ $data->member->user->location->address }}</p>
                <p class="h4 text-dark">{{ $data->member->user->location->city }}</p>
            </div>
        </div>
        <div class="row mt-4 mb-3">
            <div class="col-auto ms-auto">
                <div class="h4 text-dark">Invoice</div>
                <p class="h6 text-dark">No : {{ $data->no_invoice }}</p>
                <p class="h6 text-dark">Tanggal :
                    {{ Carbon::parse($data->updated_at)->isoFormat('LLLL') }}</p>
            </div>
        </div>
        <div class="row mt-4 mb-3">
            <h3>Detail Tamu</h3>
            <div class="col-auto">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td>{{ $data->member->user->name }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td>{{ $data->member->user->email }}</td>
                    </tr>
                    <tr>
                        <td>No. HP</td>
                        <td>:</td>
                        <td>+62{{ $data->member->user->phone_number }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row mt-4 mb-3">
            <h3>Detail Pembayaran</h3>
            @if ($data->is_approve)
                <div class="col-auto">
                    <h4><s>Belum Lunas</s>/Lunas</h4>
                </div>
            @else
                <div class="col-auto">
                    <h4>Belum Lunas/<s>Lunas</s></h4>
                </div>
            @endif
        </div>
        <div class="row mt-6 mb-3">
            <h3>Detail Transaksi</h3>
            <div class="col-12">
                @php
                    $total =
                        $data->room->category->prices->where('type', $data->duration)->first()->price +
                        ($deposit ? $deposit->jumlah : 0);
                @endphp
                <table class="table table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>No. Kamar</th>
                            <th>Kategori</th>
                            <th>Jumlah Hari</th>
                            <th>Harga Satuan</th>
                            <th>Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1.</td>
                            <td>{{ $data->room->number_room }}</td>
                            <td>{{ $data->room->category->name }}</td>
                            <td>
                                @switch($data->duration)
                                    @case('daily')
                                        Harian
                                    @break

                                    @case('weekly')
                                        Mingguan
                                    @break

                                    @case('monthly')
                                        Bulanan
                                    @break

                                    @case('yearly')
                                        Bulanan
                                    @break

                                    @default
                                @endswitch
                            </td>
                            <td>{{ Number::currency($data->room->category->prices->where('type', $data->duration)->first()->price, in: 'IDR', locale: 'id') }}
                            </td>
                            <td>{{ Number::currency($data->room->category->prices->where('type', $data->duration)->first()->price, in: 'IDR', locale: 'id') }}
                            </td>
                            @if ($deposit)
                        <tr>
                            <td>2.</td>
                            <td>{{ $data->room->number_room }}</td>
                            <td>Deposit</td>
                            <td>1</td>
                            <td>{{ Number::currency($deposit->jumlah, in: 'IDR', locale: 'id') }}
                            </td>
                            <td>{{ Number::currency($deposit->jumlah, in: 'IDR', locale: 'id') }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">Total</td>
                            <td>{{ Number::currency($total, in: 'IDR', locale: 'id') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <hr>
        <div class="row mt-6 mb-3">
            <h3>Aturan :</h3>
            <div class="col-auto">
                <ul>
                    @foreach ($data->member->user->location->rule as $value)
                        @if ($value->rule)
                            <li>{{ $value->rule->name }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        <hr>
    </div>
</body>
