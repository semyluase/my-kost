<div class="card">
    <div class="card-header">
        <h3 class="card-title">Barang Masuk</h3>
        <div class="card-actions">
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-border">
                        <thead>
                            @php
                                use Illuminate\Support\Carbon;
                                use Illuminate\Support\Number;
                            @endphp
                            <tr>
                                <th>#</th>
                                <th>No Transaksi</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($receipts as $receipt)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a
                                            href="{{ url('') }}/inventories/receipts/create?nobukti={{ $receipt->nobukti }}">{{ $receipt->nobukti }}</a>
                                    </td>
                                    <td>{{ Carbon::parse($receipt->tanggal)->isoFormat('LL') }}</td>
                                    <td>{{ Number::currency($receipt->total, in: 'IDR', locale: 'id') }}</td>
                                    <td>
                                        @if ($receipt->status == 5)
                                            <span class="badge bg-green text-green-fg">Sudah Posting</span>
                                        @else
                                            <span class="badge bg-red text-red-fg">Belum Posting</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-danger">Tidak ada Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
