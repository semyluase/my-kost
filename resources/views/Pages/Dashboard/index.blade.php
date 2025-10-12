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
            <div class="row justify-content-between mb-3">
                @livewire('Dashboard.Counter.CounterRoom')
            </div>
            <div class="row row-deck row-cards">
                <h3 class="card-title">Service</h3>
                @livewire('Dashboard.Counter.CounterOrder')
                @livewire('Dashboard.Counter.CounterLaundry')
                @livewire('Dashboard.Counter.CounterCleaning')
                @livewire('Dashboard.Counter.CounterTopup')
                @livewire('Dashboard.Table.TableRoom')
            </div>
        </div>
    </div>
@endsection
