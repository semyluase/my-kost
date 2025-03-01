@extends('Layout.main')

@section('content')
    @php
        use App\Models\Room;
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
                            @if (collect($categories)->count() > 0)
                                <div class="accordion" id="accordion-rooms">
                                    @foreach ($categories as $chunk)
                                        @foreach ($chunk as $value)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading-1">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#{{ $value->slug }}"
                                                        aria-expanded="true">
                                                        {{ $value->name }}
                                                    </button>
                                                </h2>
                                                <div id="{{ $value->slug }}" class="accordion-collapse collapse show"
                                                    data-bs-parent="#accordion-rooms" style="">
                                                    <div class="accordion-body pt-0">
                                                        @php
                                                            $rooms = collect(
                                                                Room::with(['rent', 'rent.oldRoom'])
                                                                    ->where('home_id', auth()->user()->home_id)
                                                                    ->where('category_id', $value->id)
                                                                    ->get(),
                                                            )->chunk(10);
                                                        @endphp
                                                        <div class="row">
                                                            @if (collect($rooms)->count() > 0)
                                                                @foreach ($rooms as $chunkRoom)
                                                                    @foreach ($chunkRoom as $room)
                                                                        <div class="col-4">
                                                                            @include(
                                                                                'Pages.Transaction.partials.cardsRoom',
                                                                                ['room' => $room]
                                                                            )
                                                                        </div>
                                                                    @endforeach
                                                                @endforeach
                                                            @else
                                                                <div class="col-12">
                                                                    <div class="text-center fs-1">Tidak ada daftar kamar
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center fs-1">Kategori dan Kamar masih kosong</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
