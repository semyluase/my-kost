@extends('Layout.main')

@section('content')
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
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-home" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                                    role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path
                                            d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                    </svg>
                                    Identitas Kos</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-category" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                    tabindex="-1"
                                    role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-category-2">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 4h6v6h-6z" />
                                        <path d="M4 14h6v6h-6z" />
                                        <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        <path d="M7 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    </svg>
                                    Kategori</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-room" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                    tabindex="-1"
                                    role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/activity -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-door">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 12v.01" />
                                        <path d="M3 21h18" />
                                        <path d="M6 21v-16a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v16" />
                                    </svg>
                                    Kamar</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-food-snack" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                    tabindex="-1"
                                    role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/activity -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-cup">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 11h14v-3h-14z" />
                                        <path d="M17.5 11l-1.5 10h-8l-1.5 -10" />
                                        <path d="M6 8v-1a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v1" />
                                        <path d="M15 5v-2" />
                                    </svg>
                                    Makanan & Minuman</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-home" role="tabpanel">
                                @include('Pages.Master.Home.index')
                            </div>
                            <div class="tab-pane" id="tabs-category" role="tabpanel">
                                @include('Pages.Master.Category.index')
                            </div>
                            <div class="tab-pane" id="tabs-room" role="tabpanel">
                                @include('Pages.Master.Room.index')
                            </div>
                            <div class="tab-pane" id="tabs-food-snack" role="tabpanel">
                                @include('Pages.Master.FoodSnack.index')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('myscript')
        <script>
            const tabMaster = document.querySelectorAll('a[data-bs-toggle="tab"]')
            tabMaster.forEach((tab, i) => {
                tab.addEventListener('shown.bs.tab', () => {
                    switch (i) {
                        case 0:
                            fnHome.init.tables.tbHome.columns.adjust();
                            break;
                        case 1:
                            fnCategory.init.tables.tbCategory.columns.adjust();
                            break;

                        case 2:
                            fnRoom.init.tables.tbRoom.columns.adjust();
                            break;

                        case 3:
                            fnFoodSnack.init.tables.tbFoodSnack.columns.adjust();
                            break;

                        default:
                            break;
                    }
                })
            });
        </script>
    @endpush
@endsection
