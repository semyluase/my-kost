@extends('Layout.main')

@section('content')
    @php
        use App\Models\Room;
        use App\Models\TransactionRent;
        use App\Models\TransactionHeader;
        use Illuminate\Support\Carbon;
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
            <div class="row row-deck row-cards mb-3">
                <div class="col-12">
                    <div class="card bg-blue-lt">
                        <div class="card-body">
                            <p><b>Selamat datang!</b> {{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-deck row-cards">
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <a href="{{ url('') }}/transactions/rent-rooms" class="font-weight-medium text-decoration-none">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-primary text-white avatar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="currentColor"
                                                class="icon icon-tabler icons-tabler-filled icon-tabler-bed">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M3 6a1 1 0 0 1 .993 .883l.007 .117v6h6v-5a1 1 0 0 1 .883 -.993l.117 -.007h8a3 3 0 0 1 2.995 2.824l.005 .176v8a1 1 0 0 1 -1.993 .117l-.007 -.117v-3h-16v3a1 1 0 0 1 -1.993 .117l-.007 -.117v-11a1 1 0 0 1 1 -1z" />
                                                <path
                                                    d="M7 8a2 2 0 1 1 -1.995 2.15l-.005 -.15l.005 -.15a2 2 0 0 1 1.995 -1.85z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="h1 text-primary">
                                            {{ Room::where('home_id', Auth::user()->home_id)->whereHas('rent')->count() }}/
                                            {{ Room::where('home_id', Auth::user()->home_id)->count() }} Jumlah
                                            Kamar</div>
                                        <div class="text-secondary">
                                            {{ TransactionRent::where('is_approve', false)->where('start_date', '>=', Carbon::now('Asia/Jakarta')->isoFormat('YYYY-MM-DD'))->count() }}
                                            Belum Pembayaran & Disetujui</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <a href="{{ url('') }}/transactions/rent-rooms" class="font-weight-medium text-decoration-none">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-success text-white avatar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-burger">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 15h16a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z" />
                                                <path
                                                    d="M12 4c3.783 0 6.953 2.133 7.786 5h-15.572c.833 -2.867 4.003 -5 7.786 -5z" />
                                                <path d="M5 12h14" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="h1 text-success">
                                            {{ TransactionHeader::where('tanggal', '>=', Carbon::now('Asia/Jakarta')->isoFormat('YYYY-MM-DD'))->count() }}
                                            Orderan Shop</div>
                                        <div class="text-secondary">
                                            {{ TransactionHeader::where('tanggal', '>=', Carbon::now('Asia/Jakarta')->isoFormat('YYYY-MM-DD'))->where('pembayaran', 0)->count() }}
                                            Belum Pembayaran</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
