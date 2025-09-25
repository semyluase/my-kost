<div>
    @php
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Number;
    @endphp
    <div class="row g-2 align-items-center mb-3">
        <div class="col-md-4 mb-3">
            <button class="btn btn-primary" wire:click="printTransaction()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-receipt">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path
                        d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2" />
                </svg>
                Cetak
            </button>
            <button class="btn btn-primary" wire:click="printTransactionDaily()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-receipt">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path
                        d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2" />
                </svg>
                Cetak (Hari Ini)
            </button>
        </div>
        <div class="col-md-8 ms-auto d-print-none">
            <div class="row justify-content-end">
                <div class="col-md-3 mb-3">
                    <select wire:model.live="statusService" class="form-select">
                        <option value="">All Status</option>
                        <option value="1">Terima Order</option>
                        <option value="2">Dalam Proses</option>
                        <option value="5">Selesai Order</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select wire:model.live="categoryService" class="form-select">
                        <option value="">All Order</option>
                        <option value="food">Food</option>
                        <option value="laundry">Laundry</option>
                        <option value="cleaning">Pembersihan</option>
                        <option value="top-up">Top Up</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Search">
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">
                                <label class="form-check">
                                    <input class="form-check-input border-1 border-blue" type="checkbox"
                                        wire:model="checkAllTransaction">
                                </label>
                            </th>
                            <th rowspan="2">No.</th>
                            <th rowspan="2">No. Transaksi</th>
                            <th rowspan="2">Tipe</th>
                            <th rowspan="2">No. Kamar</th>
                            <th rowspan="2">Tanggal Order</th>
                            <th rowspan="2">Total</th>
                            <th colspan="2">Status</th>
                            <th rowspan="2">#</th>
                        </tr>
                        <tr>
                            <th>Pembayaran</th>
                            <th>Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($serviceTransaction as $value)
                            <tr>
                                <td>
                                    <label class="form-check">
                                        <input class="form-check-input border-1 border-blue" type="checkbox"
                                            wire:model="checkTransaction" value="{{ $value->nobukti }}">
                                    </label>
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $value->nobukti }}</td>
                                <td>
                                    @if ($value->is_order)
                                        <span class="badge badge-lg badge-outline text-blue">Food</span>
                                    @endif

                                    @if ($value->is_laundry)
                                        <span class="badge badge-lg badge-outline text-blue">Laundry</span>
                                    @endif

                                    @if ($value->is_cleaning)
                                        <span class="badge badge-lg badge-outline text-blue">Pembersihan</span>
                                    @endif

                                    @if ($value->is_topup)
                                        <span class="badge badge-lg badge-outline text-blue">Top Up</span>
                                    @endif
                                </td>
                                <td>
                                    @isset($value->room)
                                        {{ $value->room->number_room }}
                                    @else
                                        Tidak ada kamar
                                    @endisset
                                </td>
                                <td>
                                    {{ Carbon::parse($value->tgl_request)->isoFormat('LL HH:mm:ss') }}
                                </td>
                                <td>
                                    {{ Number::currency($value->total, in: 'IDR', locale: 'id') }}
                                </td>
                                <td>
                                    @if ($value->pembayaran == 0)
                                        <span class="badge badge-lg bg-red text-red-fg">BELUM LUNAS</span>
                                    @elseif ($value->pembayaran - $value->kembalian == $value->total || $value->pembayaran != 0)
                                        <span class="badge badge-lg bg-green text-green-fg">LUNAS</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($value->is_order)
                                        @if ($value->status == 1)
                                            <span class="badge badge-lg badge-outline text-blue">ORDER MASUK</span>
                                        @elseif ($value->status == 2)
                                            <span class="badge badge-lg badge-outline text-blue">SEDANG DIBUAT</span>
                                        @endif
                                    @endif
                                    @if ($value->is_laundry)
                                        @if ($value->status == 1)
                                            <span class="badge badge-lg badge-outline text-blue">ORDER MASUK</span>
                                        @elseif ($value->status == 2)
                                            <span class="badge badge-lg badge-outline text-blue">TERIMA LAUNDRY</span>
                                        @endif
                                    @endif
                                    @if ($value->is_cleaning)
                                        @if ($value->status == 1)
                                            <span class="badge badge-lg badge-outline text-blue">ORDER MASUK</span>
                                        @elseif ($value->status == 2)
                                            <span class="badge badge-lg badge-outline text-blue">SEDANG
                                                DIBERSIHKAN</span>
                                        @endif
                                    @endif
                                    @if ($value->is_topup)
                                        @if ($value->status == 1)
                                            <span class="badge badge-lg badge-outline text-blue">ORDER MASUK</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->is_laundry)
                                        <div class="d-flex gap-2">
                                            @if ($value->status == 1)
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-primary" title="Terima Order"
                                                        wire:click="receiptOrderLaundry('{{ $value->nobukti }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-wash">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M3.486 8.965c.168 .02 .34 .033 .514 .035c.79 .009 1.539 -.178 2 -.5c.461 -.32 1.21 -.507 2 -.5c.79 -.007 1.539 .18 2 .5c.461 .322 1.21 .509 2 .5c.79 .009 1.539 -.178 2 -.5c.461 -.32 1.21 -.507 2 -.5c.79 -.007 1.539 .18 2 .5c.461 .322 1.21 .509 2 .5c.17 0 .339 -.014 .503 -.034" />
                                                            <path
                                                                d="M3 6l1.721 10.329a2 2 0 0 0 1.973 1.671h10.612a2 2 0 0 0 1.973 -1.671l1.721 -10.329" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @elseif ($value->status == 2 && ($value->pembayaran - $value->kembalian == $value->total || $value->pembayaran != 0))
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-primary" title="Finish Order"
                                                        wire:click="finishOrderLaundry('{{ $value->nobukti }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="currentColor"
                                                            class="icon icon-tabler icons-tabler-filled icon-tabler-pennant-2">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M14 2a1 1 0 0 1 .993 .883l.007 .117v17h1a1 1 0 0 1 .117 1.993l-.117 .007h-4a1 1 0 0 1 -.117 -1.993l.117 -.007h1v-7.351l-8.406 -3.735c-.752 -.335 -.79 -1.365 -.113 -1.77l.113 -.058l8.406 -3.736v-.35a1 1 0 0 1 1 -1z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @elseif ($value->status == 5)
                                                #
                                            @endif
                                            @if (!($value->pembayaran - $value->kembalian == $value->total || $value->pembayaran != 0))
                                                <button class="btn btn-success" title="Pembayaran"
                                                    wire:click="$dispatch('laundry.showModalPembayaran',{ nobukti: '{{ $value->nobukti }}' })">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                                                        <path
                                                            d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                                                        <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @elseif ($value->is_cleaning)
                                        <div class="d-flex gap-2">
                                            @if ($value->status == 1)
                                                <button class="btn btn-primary" title="Proses Pembersihan"
                                                    wire:click="processCleaning('{{ $value->nobukti }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-leaf">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M5 21c.5 -4.5 2.5 -8 7 -10" />
                                                        <path
                                                            d="M9 18c6.218 0 10.5 -3.288 11 -12v-2h-4.014c-9 0 -11.986 4 -12 9c0 1 0 3 2 5h3z" />
                                                    </svg>
                                                </button>
                                            @elseif ($value->status == 2)
                                                <button class="btn btn-primary" title="Selesai Pembersihan"
                                                    wire:click="finishProcessCleaning('{{ $value->nobukti }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-pennant">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M8 21l4 0" />
                                                        <path d="M10 21l0 -18" />
                                                        <path d="M10 4l9 4l-9 4" />
                                                    </svg>
                                                </button>
                                            @elseif ($value->status == 5 && ($value->pembayaran - $value->kembalian == $value->total || $value->pembayaran != 0))
                                                #
                                            @endif
                                            @if (!($value->pembayaran - $value->kembalian == $value->total || $value->pembayaran != 0))
                                                <button class="btn btn-success" title="Pembayaran"
                                                    wire:click="$dispatch('cleaning.showModalPembayaran',{ nobukti: '{{ $value->nobukti }}' })">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                                                        <path
                                                            d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                                                        <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @elseif ($value->is_order)
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-primary" title="Detail Order"
                                                wire:click="$dispatch('order.detailOrder',{ nobukti: '{{ $value->nobukti }}'})">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path
                                                        d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                            </button>
                                            @if ($value->status == 1)
                                                <button class="btn btn-primary" title="Proses Order"
                                                    wire:click="processOrder('{{ $value->nobukti }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-shovel-pitchforks">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M5 3h4" />
                                                        <path d="M7 3v12" />
                                                        <path d="M4 15h6v3a3 3 0 0 1 -6 0v-3z" />
                                                        <path d="M14 21v-3a3 3 0 0 1 6 0v3" />
                                                        <path d="M17 21v-18" />
                                                    </svg>
                                                </button>
                                            @elseif ($value->status == 2 && ($value->pembayaran - $value->kembalian == $value->total || $value->pembayaran != 0))
                                                <button class="btn btn-primary" title="Proses Order"
                                                    wire:click="finishOrder('{{ $value->nobukti }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-pennant">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M8 21l4 0" />
                                                        <path d="M10 21l0 -18" />
                                                        <path d="M10 4l9 4l-9 4" />
                                                    </svg>
                                                </button>
                                            @endif
                                            @if (!($value->pembayaran - $value->kembalian == $value->total || $value->pembayaran != 0))
                                                <button class="btn btn-success" title="Pembayaran"
                                                    wire:click="$dispatch('order.showModalPembayaran',{ nobukti: '{{ $value->nobukti }}' })">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                                                        <path
                                                            d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                                                        <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                @if (!empty($search))
                                    <td colspan="9">
                                        <h3 class="text-danger text-center">Tidak ada data
                                            <strong>{{ $search }}</strong>
                                        </h3>
                                    </td>
                                @else
                                    <td colspan="9">
                                        <h3 class="text-danger text-center">Tidak ada data</h3>
                                    </td>
                                @endif
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('allService.swal-modal', event => {
                    swal.fire(event[0].message, event[0].text, event[0].type)
                });
                Livewire.on('allService.generate-pdf', event => {
                    window.open(
                        `${baseUrl}/transactions/generate-receipt?search=${event[0].search}&category=${event[0].category}&nobuktiCheck=${event[0].nobuktiCheck}`
                    )
                });
            });
        </script>
    </div>

    {{-- laundry --}}
    @livewire('Service.Laundry.Payment')
    {{-- cleaning --}}
    @livewire('Service.Cleaning.Payment')
    {{-- order --}}
    @livewire('Service.Order.Detail')
    @livewire('Service.Order.Payment')
</div>
