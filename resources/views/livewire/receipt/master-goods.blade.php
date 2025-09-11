@php
    use Illuminate\Support\Str;
    use App\Models\Stock;
@endphp
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Barang</h3>
        <div class="card-actions">
            <a href="{{ url('') }}/masters" class="btn btn-primary btn-3">
                <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-2">
                    <path d="M12 5l0 14"></path>
                    <path d="M5 12l14 0"></path>
                </svg>
                Tambah Barang
            </a>
        </div>
    </div>
    <div class="card-body overflow-scroll">
        <div class="row">
            @foreach ($goods as $g)
                @php
                    $stock = Stock::where('code_item', $g->code_item)->first();
                @endphp
                <div class="col-4 mb-3">
                    <a href="javascript:;"
                        wire:click="$dispatch('addGoodsSelect',{
                            code:'{{ $g->code_item }}',
                            name:'{{ $g->name }}',
                            category:'{{ $g->category }}',
                            stock:{{ $stock ? $stock->qty : 0 }} })">
                        <div class="card" id="items">
                            <!-- Photo -->
                            @if ($g->picture)
                                <div class="img-responsive img-responsive-21x9 card-img-top"
                                    style="background-image: url(data:image/{{ Str::afterLast($g->picture->file_name, '.') }};base64,{{ $g->picture->blob }})">
                                </div>
                            @else
                                <div class="img-responsive img-responsive-21x9 card-img-top"
                                    style="background-image: url({{ asset('assets/image/nocontent.jpg') }})">
                                </div>
                            @endif
                            <div class="card-body">
                                <h3 class="card-title">{{ $g->name }} ({{ $stock ? $stock->qty : 0 }} Pcs)</h3>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
