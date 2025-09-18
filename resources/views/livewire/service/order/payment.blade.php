<div>
    <div class="modal fade modal-blur shadow {{ $showPembayaranModal ? 'show' : '' }} rounded bg-surface-backdrop"
        style="display: {{ $showPembayaranModal ? 'block' : 'hidden' }}" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran Pesanan</h5>
                    <button type="button" class="btn-close" wire:click="closeModalPembayaran()"
                        aria-label="Close"></button>
                </div>
                <form wire:submit="savePembayaran()">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="room-order" class="form-label">No. Kamar</label>
                                <h2>{{ $noRoomOrder }}</h2>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Barang</th>
                                                <th>Jumlah</th>
                                                <th>Harga Satuan</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($order)
                                                @foreach ($order->details as $value)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $value->foodSnack->name }}</td>
                                                        <td>{{ $value->qty }}</td>
                                                        <td>{{ $value->harga_jual }}</td>
                                                        <td>{{ $value->qty * $value->harga_jual }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="type-payment-order" class="form-label">Tipe Pembayaran</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" wire:model="typePaymentOrder"
                                            wire:click="onUpdatePaymentType()" value="transfer"
                                            class="form-selectgroup-input" checked="">
                                        <span class="form-selectgroup-label">Transfer</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" wire:model="typePaymentOrder"
                                            wire:click="onUpdatePaymentType()" value="cash"
                                            class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">Cash</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="price-order" class="form-label">Harga</label>
                                <input type="text" wire:model="totalPriceOrder" id="price-order"
                                    class="form-control bg-gray-500" readonly>
                            </div>
                            <div class="col-6">
                                <label for="payment-order" class="form-label">Bayar</label>
                                <input type="number" wire:model="totalPaymentOrder" wire:keydown="onUpdatePayment()"
                                    id="payment-order"
                                    class="form-control {{ $typePaymentOrder == 'transfer' ? 'bg-gray-500' : '' }}"
                                    {{ $typePaymentOrder == 'transfer' ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="" class="form-label">Kembalian</label>
                                <h2>Rp. {{ $rechargeOrder }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger me-auto" wire:click="closeModalPembayaran()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                            Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M14 4l0 4l-6 0l0 -4" />
                            </svg>
                            Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('order-payment.swal-modal', event => {
                swal.fire(event[0].message, event[0].text, event[0].type)
            });
        });
    </script>
</div>
