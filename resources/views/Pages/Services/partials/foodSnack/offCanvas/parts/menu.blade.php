@php
    use App\Models\FoodSnack;
@endphp
@if (collect($categories)->count() > 0)
    @foreach ($categories as $cat)
        <div class="hr-text hr-text-left">{{ $cat->name }}</div>
        @php
            $foodSnacks = FoodSnack::with(['stock'])
                ->where('category', $cat->short_name)
                ->get();
        @endphp
        <div class="row mb-3">
            @if (collect($foodSnacks)->count() > 0)
                @foreach ($foodSnacks as $fs)
                    <div class="col-md-6 mb-3 {{ $fs->stock->qty < 0 ? 'bg-gray-500' : '' }}">
                        <a href="javascript:;"
                            onclick="fnCreateFoodSnack.onSelectBarang('{{ $fs->code_item }}', '{{ $fs->name }}','{{ $fs->stock->harga_jual }}',{{ $fs->stock->qty }})">
                            <div class="card {{ $fs->stock->qty < 0 ? 'disabled' : '' }}">
                                <div class="row row-0">
                                    <div class="col-3">
                                        <!-- Photo -->
                                        @if ($fs->picture)
                                            <img src="data:image/{{ Str::afterLast($fs->picture->file_name, '.') }};base64,{{ $fs->picture->blob }}"
                                                class="w-100 h-100 object-cover card-img-start">
                                        @else
                                            <img src="{{ asset('assets/image/nocontent.jpg') }}"
                                                class="w-100 h-100 object-cover card-img-start">
                                        @endif
                                    </div>
                                    <div class="col">
                                        <div class="card-body">
                                            <h3 class="card-title">{{ $fs->name }}</h3>
                                            <div class="h2">Rp.
                                                {{ number_format($fs->stock->harga_jual, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach
@endif
