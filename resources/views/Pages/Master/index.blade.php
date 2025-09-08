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
                                <a href="#tabs-aturan"
                                    class="nav-link active {{ auth()->user()->role->slug == 'super-admin' ? '' : 'd-none' }}"
                                    data-bs-toggle="tab" aria-selected="true"
                                    role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-mark">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 19v.01" />
                                        <path d="M12 15v-10" />
                                    </svg>
                                    Aturan Kos</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-home"
                                    class="nav-link {{ auth()->user()->role->slug == 'super-admin' ? '' : 'd-none' }}"
                                    data-bs-toggle="tab" aria-selected="false" tabindex="-1"
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
                                <a href="#tabs-category"
                                    class="nav-link {{ auth()->user()->role->slug == 'super-admin' ? '' : 'd-none' }}"
                                    data-bs-toggle="tab" aria-selected="false" tabindex="-1"
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
                                <a href="#tabs-room"
                                    class="nav-link {{ auth()->user()->role->slug == 'super-admin' ? '' : 'd-none' }}"
                                    data-bs-toggle="tab" aria-selected="false" tabindex="-1"
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
                                <a href="#tabs-category-food-snack" class="nav-link" data-bs-toggle="tab"
                                    aria-selected="false" tabindex="-1"
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
                                    Kategori Food Snack</a>
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
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-price-cleaning" class="nav-link" data-bs-toggle="tab"
                                    aria-selected="false" tabindex="-1"
                                    role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/activity -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-brand-bilibili">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M3 10a4 4 0 0 1 4 -4h10a4 4 0 0 1 4 4v6a4 4 0 0 1 -4 4h-10a4 4 0 0 1 -4 -4v-6z" />
                                        <path d="M8 3l2 3" />
                                        <path d="M16 3l-2 3" />
                                        <path d="M9 13v-2" />
                                        <path d="M15 11v2" />
                                    </svg>
                                    Price Cleaning</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-price-laundry" class="nav-link" data-bs-toggle="tab"
                                    aria-selected="false" tabindex="-1"
                                    role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/activity -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-wash-machine">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                        <path d="M12 14m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M8 6h.01" />
                                        <path d="M11 6h.01" />
                                        <path d="M14 6h2" />
                                        <path d="M8 14c1.333 -.667 2.667 -.667 4 0c1.333 .667 2.667 .667 4 0" />
                                    </svg>
                                    Price Laundry</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-hotspot" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                    tabindex="-1" role="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-wifi">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 18l.01 0" />
                                        <path d="M9.172 15.172a4 4 0 0 1 5.656 0" />
                                        <path d="M6.343 12.343a8 8 0 0 1 11.314 0" />
                                        <path d="M3.515 9.515c4.686 -4.687 12.284 -4.687 17 0" />
                                    </svg>
                                    Hotspot</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-aturan" role="tabpanel">
                                @include('Pages.Master.Rules.index')
                            </div>
                            <div class="tab-pane" id="tabs-home" role="tabpanel">
                                @include('Pages.Master.Home.index')
                            </div>
                            <div class="tab-pane" id="tabs-category" role="tabpanel">
                                @include('Pages.Master.Category.index')
                            </div>
                            <div class="tab-pane" id="tabs-room" role="tabpanel">
                                @include('Pages.Master.Room.index')
                            </div>
                            <div class="tab-pane" id="tabs-category-food-snack" role="tabpanel">
                                @include('Pages.Master.CategoryOrder.index')
                            </div>
                            <div class="tab-pane" id="tabs-food-snack" role="tabpanel">
                                @include('Pages.Master.FoodSnack.index')
                            </div>
                            <div class="tab-pane" id="tabs-price-cleaning" role="tabpanel">
                                @include('Pages.Master.priceCleaning.index')
                            </div>
                            <div class="tab-pane" id="tabs-price-laundry" role="tabpanel">
                                @include('Pages.Master.priceLaundry.index')
                            </div>
                            <div class="tab-pane" id="tabs-hotspot" role="tabpanel">
                                @include('Pages.Master.Hotspot.index')
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
                            fnAturan.init.tables.tbRule.columns.adjust();
                            break;

                        case 1:
                            fnHome.init.tables.tbHome.columns.adjust();
                            fnHome.init.tables.tbHome.ajax
                                .url(`${baseUrl}/masters/homes/get-all-data`)
                                .load();
                            break;

                        case 2:
                            fnCategory.init.tables.tbCategory.columns.adjust();
                            break;

                        case 3:
                            fnRoom.init.tables.tbRoom.columns.adjust();
                            break;

                        case 4:
                            fnCategoryItem.init.tables.tbCategoryItem.columns.adjust();
                            break;

                        case 5:
                            fnFoodSnack.init.tables.tbFoodSnack.columns.adjust();
                            break;

                        case 6:
                            fnPriceCleaning.init.tables.tbPriceCleaning.columns.adjust();
                            break;

                        case 7:
                            fnPriceLaundry.init.tables.tbPriceLaundry.columns.adjust();
                            break;

                        default:
                            break;
                    }
                })
            });
        </script>
    @endpush
@endsection
