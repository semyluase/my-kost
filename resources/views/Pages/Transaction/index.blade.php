@extends('Layout.main')

@section('content')
    @php
        use App\Models\Room;
    @endphp
    @push('mystyles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/choicesjs/styles/choices.min.css') }}?{{ rand() }}">
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
                        <button class="btn btn-primary" id="btn-list-member" onclick="fnTransactionRoom.onListMember()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                            List Penghuni
                        </button>
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
                            <div class="row mb-3 justify-content-end d-none">
                                <div class="col-md-auto me-0">
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="btn-view" id="btn-view-1"
                                            value="table-view" autocomplete="off" checked>
                                        <label for="btn-view-1" class="btn btn-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-table">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                                                <path d="M3 10h18" />
                                                <path d="M10 3v18" />
                                            </svg>
                                        </label>
                                        <input type="radio" class="btn-check" name="btn-view" id="btn-view-2"
                                            value="calendar-view" autocomplete="off">
                                        <label for="btn-view-2" class="btn btn-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-grid-pattern">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                                <path d="M10 8v8" />
                                                <path d="M14 8v8" />
                                                <path d="M8 10h8" />
                                                <path d="M8 14h8" />
                                            </svg>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="table-view" class="mb-3">
                                <div class="row mb-3">
                                    <div class="col-3">
                                        <select name="category-room" id="category-room"
                                            class="form-select choices"></select>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table" id="tb-room" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No. Kamar</th>
                                                <th>Kos</th>
                                                <th>Kategori</th>
                                                <th>Penyewa</th>
                                                <th>Tanggal Sewa</th>
                                                <th>Status</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div id="calendar-view" class="d-none">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select name="category-calendar" id="category-calendar"
                                            class="form-select choices"></select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="room-calendar" id="room-calendar"
                                            class="form-select choices"></select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div id="calendar-section"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('Pages.Transaction.partials.index.modal.listMember')
    @include('Pages.Transaction.partials.index.modal.detailRoomTransaction')
    @push('myscript')
        <script src="{{ asset('assets/vendor/momentJS/moment.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/fullCalendar/index.global.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/choicesjs/scripts/choices.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/js/Pages/Transaction/app.js') }}?{{ rand() }}"></script>
    @endpush
@endsection
