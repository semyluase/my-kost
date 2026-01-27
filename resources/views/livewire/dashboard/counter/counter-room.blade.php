<div class="row mb-3">
    @php
        use App\Models\Room;
    @endphp
    @foreach ($categories as $category)
        @php
            $rooms = Room::CountDataByCategory($category->id)->first();

            $totalRooms = Room::where('category_id', $category->id)->count();

            if (auth()->user()->role->slug != 'super-admin') {
                $rooms = Room::CountDataByCategory($category->id, auth()->user()->home_id)->get();

                $totalRooms = Room::where('category_id', $category->id)
                    ->where('home_id', auth()->user()->home_id)
                    ->count();
            }
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
                            @if ($rooms)
                                {{ $rooms->total }}/{{ $totalRooms }}
                            @else
                                0/0
                            @endif
                        </div>
                        <div class="progress progress-sm">
                            @php
                                $totalKamar = 0;
                                $terisi = 0;
                                $percent = 0;
                                if ($rooms) {
                                    $totalKamar = $totalRooms;
                                    $terisi = $rooms->total;
                                    $percent = $totalKamar > 0 ? ($terisi / $totalKamar) * 100 : 0;
                                }
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
