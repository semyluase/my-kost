<div>
    @php
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Number;
    @endphp
    <div class="modal fade modal-blur shadow {{ $showDetailModal ? 'show' : '' }} rounded bg-surface-backdrop"
        style="display: {{ $showDetailModal ? 'block' : 'hidden' }}" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Penggunaan Saldo</h5>
                    <button type="button" class="btn-close" wire:click="closeDetailModal()"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No. Transaksi</th>
                                            <th>Jenis Transaksi</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transaksi as $t)
                                            <tr>
                                                <td>{{ $t->nobukti }}</td>
                                                <td>
                                                    @if ($t->is_order)
                                                        Shop
                                                    @elseif ($t->is_laundry)
                                                        Laundry
                                                    @elseif ($t->is_cleaning)
                                                        Pembersihan
                                                    @elseif ($t->is_topup)
                                                        Top Up Saldo
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ Carbon::parse($t->tanggal_request)->isoFormat('LL HH:mm') }}
                                                </td>
                                                <td>
                                                    {{ Number::currency($t->total, 'IDR', 'id') }}
                                                </td>
                                                <td>
                                                    @switch($t->status)
                                                        @case(1)
                                                            <span class="badge bg-blue text-blue-fg">Order Diterima</span>
                                                        @break

                                                        @case(2)
                                                            <span class="badge bg-blue text-blue-fg">Order Diproses</span>
                                                        @break

                                                        @case(5)
                                                            <span class="badge bg-blue text-blue-fg">Order Selesai</span>
                                                        @break

                                                        @default
                                                            -
                                                    @endswitch
                                                </td>
                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">
                                                        <div class="text-center">Belum ada transaksi</div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
