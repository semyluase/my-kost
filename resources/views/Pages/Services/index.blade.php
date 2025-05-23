@extends('Layout.main')

@section('content')
    @php
        use Illuminate\Support\Str;
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
                <div class="card col-card">
                    <div class="card-body">
                        <div class="hr-text hr-text-left">Service</div>
                        <div class="row mb-3">
                            @include('Pages.Services.partials.services')
                        </div>
                        <div class="hr-text hr-text-left">Food & Snack</div>
                        <div class="row mb-3">
                            @include('Pages.Services.partials.foodSnack')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
