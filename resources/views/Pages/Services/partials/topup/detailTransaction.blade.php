@php
    use Illuminate\Support\Carbon;

    Carbon::setLocale('id-ID');
@endphp
<div class="row mb-3">
    @if (collect($topups)->count() > 0)
        @foreach ($topups as $tp)
            @php
                $badge = '';

                switch ($tp->tipe_pembayaran) {
                    case 'transfer':
                        $badge = '<span class="status status-primary">Transfer</span>';
                        break;

                    default:
                        $badge = '<span class="status status-green">Cash</span>';
                        break;
                }
            @endphp
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-content-center">
                            <div class="col-12">
                                <div>
                                    <h5 class="card-title">
                                        {{ $tp->nobukti }}
                                        @if ($tp->tipe_pembayaran == 'transfer')
                                            <span class="status status-primary">Transfer</span>
                                        @elseif ($tp->tipe_pembayaran == 'cash')
                                            <span class="status status-green">Cash</span>
                                        @endif
                                    </h5>
                                    <p class="card-subtitle mt-2">
                                        {{ Carbon::parse($tp->created_at)->isoFormat('DD-MM-YYYY HH:mm') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item p-0 m-0">
                                <div class="datagrid-title">Nama Member</div>
                                <div class="datagrid-content">{{ $tp->name }}</div>
                            </div>
                            <div class="datagrid-item p-0 m-0">
                                <div class="datagrid-title">Top Up</div>
                                <div class="datagrid-content">Rp. {{ number_format($tp->qty) }}</div>
                            </div>
                            <div class="datagrid-item p-0 m-0">
                                <div class="datagrid-title">Jumlah Bayar</div>
                                <div class="datagrid-content">Rp. {{ number_format($tp->pembayaran) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
    @endif
</div>
