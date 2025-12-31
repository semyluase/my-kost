<div>
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/tabler/libs/litepicker/dist/css/litepicker.css') }}?{{ rand() }}">
    @php
        use App\Models\Deposite;
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Number;
    @endphp
    <div class="row mb-2">
        <div class="col-md-3">
            <div class="input-icon mb-2">
                <input class="form-control" placeholder="Select a date" id="tanggal-order">
                <span class="input-icon-addon"><!-- Download SVG icon from http://tabler.io/icons/icon/calendar -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-1">
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z">
                        </path>
                        <path d="M16 3v4"></path>
                        <path d="M8 3v4"></path>
                        <path d="M4 11h16"></path>
                        <path d="M11 15h1"></path>
                        <path d="M12 15v3"></path>
                    </svg></span>
            </div>
        </div>
        <div class="col-md-3">
            <select wire:model.live="homeID"
                class="form-select {{ auth()->user()->role->slug == 'super-admin' || auth()->user()->role->slug == 'admin' ? '' : 'bg-gray-500' }}"
                {{ auth()->user()->role->slug == 'super-admin' ? '' : 'disabled' }} id="homeID">
                <option value="">Pilih Alamat</option>
                @if ($homeList)
                    @foreach ($homeList as $h)
                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-3">
            <button class="btn btn-primary" onclick="searchData()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                    <path d="M21 21l-6 -6" />
                </svg>
                Tampilkan
            </button>
            <button class="btn btn-primary {{ $loading ? 'disabled' : '' }}" {{ $loading ? 'disabled' : '' }}
                onclick="downloadData()">
                @if (!$loading)
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-xls">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                        <path d="M4 15l4 6" />
                        <path d="M4 21l4 -6" />
                        <path
                            d="M17 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75" />
                        <path d="M11 15v6h3" />
                    </svg>
                    Unduh
                @else
                    <div class="spinner-border text-green" role="status"></div>
                    Downloading...
                @endif
            </button>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-borderless">
                    <div class="card-header">
                        <h3 class="card-title">Sewa Kamar</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-selectable card-table table-vcenter text-nowrap datatable">
                            <thead>
                                <tr>
                                    <th rowspan="2">No. Kamar</th>
                                    <th rowspan="2">Kategori</th>
                                    <th rowspan="2">Jenis Transaksi</th>
                                    <th rowspan="2">Tanggal Pemesanan</th>
                                    <th rowspan="2">Jenis Pembayaran</th>
                                    <th rowspan="2">Status</th>
                                    <th colspan="2">Jumlah</th>
                                </tr>
                                <tr>
                                    <th>Pemasukan</th>
                                    <th>Pengeluaran</th>
                                </tr>
                            </thead>
                            @php
                                $totalPemasukan = 0;
                                $totalPengeluaran = 0;
                            @endphp
                            <tbody>
                                @forelse ($rents as $rent)
                                    <tr>
                                        <td>{{ $rent->room->number_room }}</td>
                                        <td>{{ $rent->room->category->name }}</td>
                                        <td>
                                            @if ($rent->is_check_in)
                                                Check In
                                            @elseif ($rent->is_check_out)
                                                Check Out
                                            @elseif ($rent->is_deposit)
                                                Deposit
                                            @elseif ($rent->is_upgrade)
                                                Upgrade Kamar
                                            @elseif ($rent->is_downgrade)
                                                Downgrade Kamar
                                            @elseif ($rent->is_credit)
                                                Saldo Dompet Digital
                                            @endif
                                        </td>
                                        <td>{{ Carbon::parse($rent->tgl)->isoFormat('LL') }}</td>
                                        <td>{{ Str::upper($rent->payment_type) }}</td>
                                        <td>
                                            @if ($rent->is_check_in)
                                                Check In
                                            @elseif ($rent->is_check_out)
                                                Check Out
                                                <div>Bank : {{ $rent->bank }}</div>
                                                <div>Rekening : {{ $rent->rekening }}</div>
                                            @elseif ($rent->is_deposit)
                                                Deposit
                                            @elseif ($rent->is_upgrade)
                                                Upgrade Kamar
                                            @elseif ($rent->is_downgrade)
                                                Downgrade Kamar
                                            @elseif ($rent->is_credit)
                                                Pengembalian Saldo
                                            @endif
                                        </td>
                                        <td>
                                            @if ($rent->is_check_in)
                                                {{ Number::currency($rent->jumlah, in: 'IDR', locale: 'id') }}
                                                @php
                                                    $totalPemasukan += $rent->jumlah;
                                                @endphp
                                            @elseif ($rent->is_deposit)
                                                {{ Number::currency($rent->jumlah, in: 'IDR', locale: 'id') }}
                                                @php
                                                    $totalPemasukan += $rent->jumlah;
                                                @endphp
                                            @elseif ($rent->is_upgrade)
                                                {{ Number::currency($rent->jumlah, in: 'IDR', locale: 'id') }}
                                                @php
                                                    $totalPemasukan += $rent->jumlah;
                                                @endphp
                                            @else
                                                {{ Number::currency(0, in: 'IDR', locale: 'id') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($rent->is_check_out)
                                                {{ Number::currency($rent->jumlah, in: 'IDR', locale: 'id') }}

                                                @php
                                                    $totalPengeluaran += $rent->jumlah;
                                                @endphp
                                            @elseif ($rent->is_credit)
                                                {{ Number::currency($rent->jumlah, in: 'IDR', locale: 'id') }}

                                                @php
                                                    $totalPengeluaran += $rent->jumlah;
                                                @endphp
                                            @else
                                                {{ Number::currency(0, in: 'IDR', locale: 'id') }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">
                                            <div class="text-danger text-center h3">Tidak ada data sewa kamar</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6">Total</th>
                                    <th>{{ Number::currency($totalPemasukan, in: 'IDR', locale: 'id') }}</th>
                                    <th>{{ Number::currency($totalPengeluaran, in: 'IDR', locale: 'id') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-borderless">
                    <div class="card-header">
                        <h3 class="card-title">Layanan Lain</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-selectable card-table table-vcenter text-nowrap datatable">
                            <thead>
                                <tr>
                                    <th rowspan="2">No. Kamar</th>
                                    <th rowspan="2">Tanggal Pemesanan</th>
                                    <th rowspan="2">Jenis Layanan</th>
                                    <th rowspan="2">Jenis Pembayaran</th>
                                    <th rowspan="2">Status</th>
                                    <th colspan="3">Jumlah</th>
                                </tr>
                                <tr>
                                    <th>Outstanding</th>
                                    <th>Pemasukan</th>
                                    <th>Pengeluaran</th>
                                </tr>
                            </thead>
                            @php
                                $totalOutstanding = 0;
                                $totalPemasukan = 0;
                                $totalPengeluaran = 0;
                            @endphp
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->room ? $transaction->room->number_room : '-' }}</td>
                                        <td>{{ Carbon::parse($transaction->tanggal)->isoFormat('LL') }}</td>
                                        <td>
                                            @if ($transaction->is_order)
                                                Food
                                            @elseif ($transaction->is_laundry)
                                                Laundry
                                            @elseif ($transaction->is_cleaning)
                                                Pembersihan
                                            @elseif ($transaction->is_topup)
                                                Topup Saldo
                                            @elseif ($transaction->is_receipt)
                                                Pembelian Barang
                                            @endif
                                        </td>
                                        <td>{{ Str::upper($transaction->tipe_pembayaran) }}</td>
                                        <td>{{ !$transaction->is_receipt ? ($transaction->pembayaran > 0 ? 'LUNAS' : 'BELUM LUNAS') : ($transaction->status == 5 ? 'SUDAH POSTING' : 'BELUM POSTING') }}
                                        </td>
                                        <td>
                                            @if ($transaction->is_receipt)
                                                @if ($transaction->status == 1)
                                                    {{ Number::currency($transaction->total, in: 'IDR', locale: 'id') }}
                                                    @php
                                                        $totalOutstanding += $transaction->total;
                                                    @endphp
                                                @else
                                                    {{ Number::currency(0, in: 'IDR', locale: 'id') }}
                                                @endif
                                            @else
                                                @if (!$transaction->is_receipt && $transaction->pembayaran == 0)
                                                    {{ Number::currency($transaction->total, in: 'IDR', locale: 'id') }}
                                                    @php
                                                        $totalOutstanding += $transaction->total;
                                                    @endphp
                                                @else
                                                    {{ Number::currency(0, in: 'IDR', locale: 'id') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$transaction->is_receipt && $transaction->pembayaran > 0)
                                                {{ Number::currency($transaction->total, in: 'IDR', locale: 'id') }}
                                                @php
                                                    $totalPemasukan += $transaction->total;
                                                @endphp
                                            @else
                                                {{ Number::currency(0, in: 'IDR', locale: 'id') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->is_receipt)
                                                @if ($transaction->status == 5)
                                                    {{ Number::currency($transaction->total, in: 'IDR', locale: 'id') }}
                                                    @php
                                                        $totalPengeluaran += $transaction->total;
                                                    @endphp
                                                @else
                                                    {{ Number::currency(0, in: 'IDR', locale: 'id') }}
                                                @endif
                                            @else
                                                {{ Number::currency(0, in: 'IDR', locale: 'id') }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="text-danger text-center h3">Tidak ada data transaksi</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total</th>
                                    <th>{{ Number::currency($totalOutstanding, in: 'IDR', locale: 'id') }}</th>
                                    <th>{{ Number::currency($totalPemasukan, in: 'IDR', locale: 'id') }}</th>
                                    <th>{{ Number::currency($totalPengeluaran, in: 'IDR', locale: 'id') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/vendor/momentJS/moment-with-locales.min.js') }}?{{ rand() }}"></script>
    <script src="{{ asset('assets/vendor/tabler/libs/litepicker/dist/bundle.js') }}?{{ rand() }}"></script>
    <script>
        const tanggalOrder = new Litepicker({
            element: document.querySelector("#tanggal-order"),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
            },
            format: "DD/MM/YYYY",
            singleMode: false,
            startDate: moment('{{ $startDate }}'),
            endDate: moment('{{ $endDate }}'),
            lang: "id-ID",
        })

        let selectedHomeID = document.querySelector('#homeID');

        const searchData = () => {
            Livewire.dispatch('report.searchReport', {
                startDate: `${moment(tanggalOrder
                        .getStartDate().toJSDate()).format("YYYY-MM-DD")}`,
                endDate: `${moment(tanggalOrder
                        .getEndDate().toJSDate()).format("YYYY-MM-DD")}`,
                homeID: selectedHomeID.value
            })
        }

        const downloadData = () => {
            Livewire.dispatch('report.downloadReport', {
                startDate: `${moment(tanggalOrder
                        .getStartDate().toJSDate()).format("YYYY-MM-DD")}`,
                endDate: `${moment(tanggalOrder
                        .getEndDate().toJSDate()).format("YYYY-MM-DD")}`,
                homeID: selectedHomeID.value
            })
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('report.generateExcel', async event => {
                await fetch(
                        `${baseUrl}/reports/generate-data?s=${event[0].startDate}&e=${event[0].endDate}&home=${event[0].homeID}`
                    )
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error ' + response.status);
                        }

                        const disposition = response.headers.get('Content-Disposition');
                        if (disposition && disposition.includes('filename=')) {
                            const match = disposition.match(/filename="?([^"]+)"?/);
                            if (match && match[1]) {
                                event[0].filename = match[1];
                            }
                        }

                        return response.blob();
                    })
                    .then(blob => {
                        const link = document.createElement('a');
                        const href = window.URL.createObjectURL(blob);

                        link.href = href;
                        link.download = event[0].filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(href);

                        Livewire.dispatch('report.loading');
                    })
                    .catch(err => {
                        console.error('Gagal download:', err);
                        alert('Gagal mengunduh file');
                    });
            });
        })
    </script>
</div>
