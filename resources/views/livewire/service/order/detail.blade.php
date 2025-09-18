<div>
    @php
        use Illuminate\Support\Number;
    @endphp
    <div class="modal fade modal-blur shadow {{ $showModalDetail ? 'show' : '' }} rounded bg-surface-backdrop"
        style="display: {{ $showModalDetail ? 'block' : 'hidden' }}" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Order</h5>
                    <button type="button" class="btn-close" wire:click="closeModalDetail()"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($order)
                        <div class="row mb-3 justify-content-between">
                            <div class="col-6">
                                <label class="form-label">No. Kamar</label>
                                <div class="h3">{{ $order->details->first()->no_room }}</div>
                            </div>
                            <div class="col-6 justify-content-end">
                                <h2>
                                    @if ($order->pembayaran - $order->kembalian == $order->total)
                                        <span class="badge bg-green text-green-fg">Lunas</span>
                                    @else
                                        <span class="badge bg-red text-red-fg">Belum Lunas</span>
                                    @endif
                                </h2>
                            </div>
                        </div>
                    @endif
                    <div class="row mb-12">
                        <div class="col-12">
                            <table class="table table-borderless">
                                <thead class="fw-bold">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                    @endphp
                                    @if ($order)
                                        @forelse ($order->details as $value)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $value->foodSnack->name }}</td>
                                                <td>{{ $value->qty }}</td>
                                                <td>{{ Number::currency($value->harga_jual, in: 'IDR', locale: 'id') }}
                                                </td>
                                                <td>{{ Number::currency($value->qty * $value->harga_jual, in: 'IDR', locale: 'id') }}
                                                </td>
                                                @php
                                                    $total += $value->qty * $value->harga_jual;
                                                @endphp
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">
                                                    <div class="text-center text-danger fw-bold">Tidak ada data
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    @else
                                        <tr>
                                            <td colspan="5">
                                                <div class="text-center text-danger fw-bold">Tidak ada data</div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">
                                            Total
                                        </td>
                                        <td>{{ Number::currency($total, in: 'IDR', locale: 'id') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
