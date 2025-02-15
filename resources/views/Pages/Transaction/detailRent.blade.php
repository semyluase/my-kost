@extends('Layout.main')

@section('content')
    @php
        use App\Models\Room;
        use App\Models\Deposite;
        use Illuminate\Support\Carbon;
        Carbon::setLocale('id_ID');
    @endphp
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        {{ $pageTitle }}
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 mb-3">
                                    <div class="row d-flex justify-between">
                                        <div class="col-6 mb-3">
                                            <h4 class="h4">Detail Kost</h4>
                                            <p class="h5">{{ auth()->user()->location->name }}</p>
                                            <p>{{ auth()->user()->location->address }}</p>
                                        </div>
                                        <div class="col-6 mb-3 text-end">
                                            @php
                                                $rent->member->load(['user']);
                                            @endphp
                                            <h4 class="h4">Detail Penghuni</h4>
                                            <p class="h5">{{ $rent->member->user->name }}</p>
                                            <p>{{ $rent->member->address }}</p>
                                            <p>0{{ $rent->member->phone_number }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>No. Kamar</th>
                                                <th>Kategori</th>
                                                <th>Durasi</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>{{ $rent->room->number_room }}</td>
                                                <td>{{ $rent->room->category->name }}</td>
                                                <td>
                                                    @switch($rent->duration)
                                                        @case('daily')
                                                            Harian
                                                        @break

                                                        @case('monthly')
                                                            Bulanan
                                                        @break

                                                        @case('weekly')
                                                            Mingguan
                                                        @break

                                                        @default
                                                            tahunan
                                                    @endswitch
                                                    <p>
                                                        {{ Carbon::parse($rent->start_date)->isoFormat('DD MMMM YYYY') }}
                                                    </p>
                                                    <p>
                                                        {{ Carbon::parse($rent->end_date)->isoFormat('DD MMMM YYYY') }}
                                                    </p>
                                                </td>
                                                <td>{{ $rent->price }}</td>
                                            </tr>
                                            @php
                                                $deposit = Deposite::where('room_id', $rent->room->id)
                                                    ->where('user_id', $rent->member->user->id)
                                                    ->first();
                                            @endphp
                                            @if (!$deposit)
                                                <tr>
                                                    <td>2</td>
                                                    <td>Deposit {{ $rent->room->number_room }}</td>
                                                    <td>{{ $rent->room->category->name }}</td>
                                                    <td>
                                                        <p>
                                                            Deposit berlaku sampai penghuni tidak memperpanjang sewa
                                                        </p>
                                                    </td>
                                                    <td>{{ $rent->price }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">Total</td>
                                                <td>
                                                    @if ($deposit)
                                                        {{ $rent->price }}
                                                    @else
                                                        {{ $rent->price * 2 }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 d-flex">
                                    <button class="btn btn-primary ms-auto" data-csrf="{{ csrf_token() }}"
                                        data-room="{{ $rent->room->number_room }}" id="btn-save">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path d="M14 4l0 4l-6 0l0 -4" />
                                        </svg>
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('myscript')
        <script src="{{ asset('assets/js/Pages/Transaction/detail/app.js') }}?{{ rand() }}"></script>
    @endpush
@endsection
