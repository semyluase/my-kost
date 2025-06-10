@extends('Layout.main')

@section('content')
    @php
        use App\Models\Room;
        use App\Models\Deposite;
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Number;
        use Illuminate\Support\Str;
        Carbon::setLocale('id_ID');
    @endphp
    @push('mystyles')
        <style>
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            input[type=number] {
                -moz-appearance: textfield;
            }
        </style>
    @endpush
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
                                @if ($rent->oldRoom)
                                    @if ($rent->oldRoom->oldRent->is_upgrade)
                                        <div class="col-12 mb-3">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>No. Kamar</th>
                                                            <th>Kategori</th>
                                                            <th>Tipe Transaksi</th>
                                                            <th>Durasi</th>
                                                            <th>Detail Sewa</th>
                                                            <th>Harga Sewa</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>1</td>
                                                            <td>{{ $rent->room->number_room }}</td>
                                                            <td>{{ $rent->room->category->name }}</td>
                                                            <td>Upgrade</td>
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
                                                            <td>
                                                                Sisa Hari Sewa : {{ $rent->sisa_hari_sewa }}
                                                                <br>
                                                                Total Hari Sewa : {{ $rent->total_hari_sewa }}
                                                            </td>
                                                            <td>
                                                                Harga Sewa lama :
                                                                {{ Number::currency($rent->oldRoom->oldRent->price, 'Rp.', 'id') }}
                                                                <br>
                                                                Harga Sewa Baru :
                                                                {{ Number::currency($rent->price, 'Rp.', 'id') }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $deposit = Deposite::where(
                                                                        'room_id',
                                                                        $rent->room->id,
                                                                    )
                                                                        ->where('user_id', $rent->member->user->id)
                                                                        ->where('is_checkout', false)
                                                                        ->first();
                                                                @endphp
                                                                {{ Number::currency($rent->kurang_bayar, 'Rp.', 'id') }}
                                                                <input type="hidden" name="deposit" id="deposit"
                                                                    class="form-control {{ $deposit ? 'bg-gray-500' : '' }}"
                                                                    {{ $deposit ? 'readonly' : '' }}
                                                                    value="{{ $deposit ? $deposit->jumlah : $rent->price }}">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot class="bg-gray-100">
                                                        <tr>
                                                            <td colspan="7">Sub Total</td>
                                                            <td>
                                                                {{ Number::currency($rent->kurang_bayar, 'Rp.', 'id') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">Pembulatan</td>
                                                            <td>
                                                                {{ Number::currency($rent->pembulatan, 'Rp.', 'id') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">Total</td>
                                                            <td>
                                                                <input type="hidden" class="form-control bg-gray-400"
                                                                    id="total" readonly value="">
                                                                {{ Number::currency($rent->kurang_bayar + $rent->pembulatan, 'Rp.', 'id') }}
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="col-12 mb-3">
                                        <div class="table-responsive">
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
                                                                    Tahunan
                                                            @endswitch
                                                            <p>
                                                                {{ Carbon::parse($rent->start_date)->isoFormat('DD MMMM YYYY') }}
                                                            </p>
                                                            <p>
                                                                {{ Carbon::parse($rent->end_date)->isoFormat('DD MMMM YYYY') }}
                                                            </p>
                                                        </td>
                                                        <td>{{ Str::replaceEnd(',00', '', Number::currency($rent->price, 'Rp.', 'id')) }}
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $deposit = Deposite::where('room_id', $rent->room->id)
                                                            ->where('user_id', $rent->member->user->id)
                                                            ->where('is_checkout', false)
                                                            ->first();
                                                    @endphp
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Deposit {{ $rent->room->number_room }}</td>
                                                        <td>{{ $rent->room->category->name }}</td>
                                                        <td>
                                                            <p>
                                                                Deposit berlaku sampai penghuni tidak memperpanjang
                                                                sewa/Checkout
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" name="deposit" id="deposit"
                                                                    class="form-control {{ $deposit ? 'bg-gray-500' : '' }}"
                                                                    {{ $deposit ? 'readonly' : '' }}
                                                                    value="{{ $deposit ? $deposit->jumlah : $rent->price }}">
                                                                <button class="btn btn-primary"
                                                                    {{ $deposit ? 'disabled' : '' }}
                                                                    onclick="fnDetailSewa.clearDeposit()">
                                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/search -->
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none" />
                                                                        <path d="M18 6l-12 12" />
                                                                        <path d="M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4">Total</td>
                                                        <td>
                                                            <input type="text" name="total" disabled id="total"
                                                                class="form-control">
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                @endif
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
        <script>
            const price = {{ $rent->price }}
        </script>
        <script src="{{ asset('assets/js/Pages/Transaction/detail/app.js') }}?{{ rand() }}"></script>
    @endpush
@endsection
