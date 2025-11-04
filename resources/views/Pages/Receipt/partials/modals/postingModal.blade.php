<div class="modal fade {{ $showModal ? 'show' : '' }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document"
        style="{{ $showModal ? 'display:block' : 'display:hidden' }}">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" wire:click="closeModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="text-center text-danger">
                            Ingin memosting Receipt Inventory ini? Harap pilih tipe pembayaran yang anda gunakan
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" wire:model="paymentType" autocomplete="off">
                        <label for="btn-radio-basic-1" type="button" class="btn" value="cash">Cash</label>
                        <input type="radio" class="btn-check" wire:model="paymentType" autocomplete="off">
                        <label for="btn-radio-basic-1" type="button" class="btn" value="transfer">Transfer</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger me-auto" wire:click="closeModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="postingReceipt">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-xls">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                            <path d="M4 15l4 6" />
                            <path d="M4 21l4 -6" />
                            <path
                                d="M17 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75" />
                            <path d="M11 15v6h3" />
                        </svg>
                        Posting
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
