<div>
    @php
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Number;
    @endphp
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/tabler/libs/litepicker/dist/css/litepicker.css') }}?{{ rand() }}">
    <div class="row g-2 align-items-center mb-3">
        <div class="col-md-8 ms-auto d-print-none">
            <div class="row justify-content-end">
                <div class="col-md-3 mb-3">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="form-control border-1 border-primary" placeholder="Search">
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        @forelse ($users as $user)
            <div class="col-sm-6 col-lg-3 mb-3">
                <a href="javascript:;" class="text-dark text-decoration-none"
                    wire:click="$dispatch('detail.showModal',{ userID: '{{ $user->id }}' })">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="h1">{{ $user->name }}</div>
                            </div>
                            <div class="h3 mb-3">{{ Number::currency($user->credit->credit, 'IDR', 'id') }}</div>
                        </div>
                        <div class="card-footer">
                            <h5 class="text-primary">Click untuk melihat detail</h5>
                        </div>
                    </div>
                </a>
            </div>
        @empty
        @endforelse
        {{-- <script src="{{ asset('assets/vendor/momentJS/moment-with-locales.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/tabler/libs/litepicker/dist/bundle.js') }}?{{ rand() }}"></script>
        <script>
            const tanggalSearch = new Litepicker({
                element: document.querySelector("#search-date"),
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

            const searchData = () => {
                Livewire.dispatch('allService.searchData', {
                    startDate: `${moment(tanggalSearch
                    .getStartDate().toJSDate()).format("YYYY-MM-DD")}`,
                    endDate: `${moment(tanggalSearch
                    .getEndDate().toJSDate()).format("YYYY-MM-DD")}`
                })
            }

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
        </script> --}}
    </div>

    @livewire('Member.Credit.Detail')
</div>
