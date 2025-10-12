<div class="row mb-3">
    @php
        use App\Models\Room;
    @endphp
    @foreach ($categories as $category)
        @php
            $rooms = Room::with(['rentToday'])
                ->where('category_id', $category->id)
                ->get();
        @endphp
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ url('') }}/transactions/rent-rooms" class="text-decoration-none text-dark">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="h4 text-dark">{{ $category->name }}</div>
                            <div class="ms-auto lh-1">
                                <div class="text-secondary">Hari ini</div>
                            </div>
                        </div>
                        <div class="h1 mb-3">
                            {{ count(collect($rooms)->filter(fn($item) => $item->rentToday != null)) }}/{{ collect($rooms)->count() }}
                        </div>
                        <div class="progress progress-sm">
                            @php
                                $totalKamar = collect($rooms)->count();
                                $terisi = count(collect($rooms)->filter(fn($item) => $item->rentToday != null));
                                $percent = ($terisi / $totalKamar) * 100;
                            @endphp
                            <div class="progress-bar bg-success" style="width: {{ $percent }}%" role="progressbar"
                                aria-valuenow="{{ $terisi }}" aria-valuemin="0"
                                aria-valuemax="{{ $totalKamar }}" aria-label="{{ $percent }} Terisi">
                                <span class="visually-hidden">{{ $percent }}% Terisi</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endforeach
</div>
