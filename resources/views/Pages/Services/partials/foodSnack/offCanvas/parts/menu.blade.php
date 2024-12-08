@php
    use App\Models\Stock;
@endphp
@if (collect($categories)->count() > 0)
    @foreach ($categories as $cat)
        @switch($cat->kategori)
            @case('F')
                @php
                    $tipe = 'Makanan';
                @endphp
            @break

            @case('D')
                @php
                    $tipe = 'Minuman';
                @endphp
            @break

            @default
                @php
                    $tipe = 'Makanan Ringan';
                @endphp
        @endswitch
        <div class="hr-text hr-text-left">{{ $tipe }}</div>
        @php
            $stocks = Stock::with(['foodSnack'])
                ->where('kategori', $cat->kategori)
                ->get();
        @endphp
        <div class="row mb-3">
            @if (collect($stocks)->count() > 0)
                @foreach ($stocks as $stock)
                    <div class="col-6 mb-3 {{ $stock->qty < 0 ? 'bg-gray-500' : '' }}">
                        <a href="javascript:;"
                            onclick="fnCreateFoodSnack.onSelectBarang('{{ $stock->foodSnack->code_item }}', '{{ $stock->foodSnack->name }}','{{ $stock->harga_jual }}',{{ $stock->qty }})">
                            <div class="card {{ $stock->qty < 0 ? 'disabled' : '' }}">
                                <div class="row row-0">
                                    <div class="col-3">
                                        <!-- Photo -->
                                        <img src="data:image/{{ Str::afterLast($stock->foodSnack->picture->file_name, '.') }};base64,{{ $stock->foodSnack->picture->blob }}"
                                            class="w-100 h-100 object-cover card-img-start">
                                    </div>
                                    <div class="col">
                                        <div class="card-body">
                                            <h3 class="card-title">{{ $stock->foodSnack->name }}</h3>
                                            <div class="h2">Rp.
                                                {{ number_format($stock->harga_jual, 0, ',', '.') }}</div>
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
