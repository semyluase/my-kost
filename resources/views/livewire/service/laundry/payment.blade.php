<div>
    <div class="modal fade modal-blur shadow {{ $showPembayaranLaundryModal ? 'show' : '' }} rounded bg-surface-backdrop"
        style="display: {{ $showPembayaranLaundryModal ? 'block' : 'hidden' }}" tabindex="-1" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran Laundry</h5>
                    <button type="button" class="btn-close" wire:click="closeModalPembayaran()"
                        aria-label="Close"></button>
                </div>
                <form wire:submit="savePembayaran()">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="room-laundry" class="form-label">No. Kamar</label>
                                <h2>{{ $roomLaundry }}</h2>
                            </div>
                            <div class="col-6">
                                <label for="category-laundry" class="form-label">Nama Paket</label>
                                <input type="text" value="{{ $categoryLaundry }}" class="form-control bg-gray-500"
                                    id="category-laundry" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="qty-laundry" class="form-label">Berat Paket</label>
                                <input type="text" value="{{ $beratLaundry }}" class="form-control bg-gray-500"
                                    id="qty-laundry" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="type-payment-laundry" class="form-label">Tipe Pembayaran</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" wire:model="typePaymentLaundry" value="transfer"
                                            class="form-selectgroup-input" checked="">
                                        <span class="form-selectgroup-label">Transfer</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" wire:model="typePaymentLaundry" value="cash"
                                            class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">Cash</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="price-laundry" class="form-label">Harga</label>
                                <input type="text" wire:model="priceLaundry" id="price-laundry"
                                    class="form-control bg-gray-500" readonly>
                            </div>
                            <div class="col-6">
                                <label for="payment-laundry" class="form-label">Bayar</label>
                                <input type="number" wire:model="paymentLaundry" wire:keydown="onUpdatePayment()"
                                    id="payment-laundry" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="" class="form-label">Kembalian</label>
                                <h2>Rp. {{ $rechargeLaundry }}</h2>
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
            Livewire.on('payment.swal-modal', event => {
                swal.fire(event[0].message, event[0].text, event[0].type)
            });
        });
    </script>
</div>
