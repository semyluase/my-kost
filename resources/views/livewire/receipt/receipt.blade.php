<div class="card">
    <div class="card-header">
        <h3 class="card-title">Barang Masuk</h3>
        <div class="card-actions">
            <button class="btn btn-primary" wire:click="$dispatch('stockList.showModal')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-stack-2">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 4l-8 4l8 4l8 -4l-8 -4" />
                    <path d="M4 12l8 4l8 -4" />
                    <path d="M4 16l8 4l8 -4" />
                </svg>
                Stock
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <select wire:model.live="homeID" class="form-select"
                    {{ auth()->user()->role->slug == 'super-admin' || auth()->user()->role->slug == 'admin' ? '' : 'readonly' }}>
                    <option value="">Pilih Alamat</option>
                    @if ($homeList)
                        @foreach ($homeList as $h)
                            <option value="{{ $h->id }}">{{ $h->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
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
    @livewire('Receipt.StockList')
</div>
