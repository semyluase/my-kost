<div class="row mb-3">
    <div class="col-12">
        <h4>Pembayaran</h4>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <h1>#{{ $header->nobukti }}</h1>
    </div>
    <table class="table table-transparent table-responsive">
        <thead>
            <tr>
                <th class="text-center" style="width: 2%">#</th>
                <th>Barang</th>
                <th class="text-center" style="width: 2%">Jumlah</th>
                <th class="text-center">Harga Satuan</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($header->details as $detail)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <p class="strong mb-1">{{ $detail->foodSnack->name }}</p>
                    </td>
                    <td class="text-center">
                        {{ $detail->qty }}
                    </td>
                    <td class="text-end">Rp. {{ number_format($detail->harga_jual, 2, ',', '.') }}</td>
                    <td class="text-end">Rp. {{ number_format($detail->qty * $detail->harga_jual, 2, ',', '.') }}
                    </td>
                </tr>
                @php
                    $total += $detail->qty * $detail->harga_jual;
                @endphp
            @endforeach
            <tr>
                <td colspan="4" class="strong text-end">Total</td>
                <td class="text-end" id="total-harga-payment">Rp. {{ number_format($total, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>
