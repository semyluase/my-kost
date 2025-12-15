<div>
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/tabler/libs/litepicker/dist/css/litepicker.css') }}?{{ rand() }}">
    @php
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Number;
    @endphp
    <div class="row mb-2">
        <div class="col-md-3 mb-3">
            <div class="input-icon mb-2">
                <input class="form-control" placeholder="Select a date" id="tanggal-checkout">
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
        <div class="col-md-3 mb-3">
            <select wire:model.live="homeIDCheckout"
                class="form-select {{ auth()->user()->role->slug == 'super-admin' || auth()->user()->role->slug == 'admin' ? '' : 'bg-gray-500' }}"
                {{ auth()->user()->role->slug == 'super-admin' ? '' : 'disabled' }} id="homeIDCheckout">
                <option value="">Pilih Alamat</option>
                @if ($homeListCheckout)
                    @foreach ($homeListCheckout as $h)
                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <button class="btn btn-primary" onclick="searchDataCheckout()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                    <path d="M21 21l-6 -6" />
                </svg>
                Tampilkan
            </button>
            <button class="btn btn-primary {{ $loadingCheckout ? 'disabled' : '' }}"
                {{ $loadingCheckout ? 'disabled' : '' }} onclick="downloadDataCheckout()">
                @if (!$loadingCheckout)
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
        <div class="col-md-12 mb-3">
            <div class="table-responsive">
                <table class="table table-selectable card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>No. Kamar</th>
                            <th>Nama Penyewa</th>
                            <th>No. HP Penyewa</th>
                            <th>Tanggal Checkout</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactionRent as $rent)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $rent->room->number_room }}</td>
                                <td>{{ $rent->member->user->name }}</td>
                                <td>{{ $rent->member->phone_number }}</td>
                                <td>{{ Carbon::parse($rent->end_date)->isoFormat('LL') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-danger">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/vendor/momentJS/moment-with-locales.min.js') }}?{{ rand() }}"></script>
    <script src="{{ asset('assets/vendor/tabler/libs/litepicker/dist/bundle.js') }}?{{ rand() }}"></script>
    <script>
        const tanggalCheckout = new Litepicker({
            element: document.querySelector("#tanggal-checkout"),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
            },
            format: "DD/MM/YYYY",
            singleMode: false,
            startDate: moment('{{ $startDateCheckout }}'),
            endDate: moment('{{ $endDateCheckout }}'),
            lang: "id-ID",
        })

        let selectedHomeIDCheckout = document.querySelector('#homeIDCheckout');

        const searchDataCheckout = () => {
            Livewire.dispatch('listCheckout.searchReport', {
                startDate: `${moment(tanggalCheckout
                        .getStartDate().toJSDate()).format("YYYY-MM-DD")}`,
                endDate: `${moment(tanggalCheckout
                        .getEndDate().toJSDate()).format("YYYY-MM-DD")}`,
                homeID: selectedHomeIDCheckout.value
            })
        }

        const downloadDataCheckout = () => {
            Livewire.dispatch('listCheckout.downloadReport', {
                startDate: `${moment(tanggalCheckout
                        .getStartDate().toJSDate()).format("YYYY-MM-DD")}`,
                endDate: `${moment(tanggalCheckout
                        .getEndDate().toJSDate()).format("YYYY-MM-DD")}`,
                homeID: selectedHomeIDCheckout.value
            })
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('listCheckout.generateExcel', async event => {
                await fetch(
                        `${baseUrl}/reports/generate-data-checkout?s=${event[0].startDateCheckout}&e=${event[0].endDateCheckout}&home=${event[0].homeIDCheckout}`
                    )
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP error ' + response.status);
                            Livewire.dispatch('listCheckout.loading');
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

                        Livewire.dispatch('listCheckout.loading');
                    })
                    .catch(err => {
                        console.error('Gagal download:', err);
                        alert('Gagal mengunduh file');
                        Livewire.dispatch('listCheckout.loading');
                    });
            });
        })
    </script>
</div>
