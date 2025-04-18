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
            @foreach ($foodSnacks as $fs)
                @php
                    $stock = Stock::where('code_item', $fs->code_item)->first();
                @endphp
                <div class="col-4 mb-3">
                    <a href="javascript:;"
                        onclick="fnReceipt.onSelectGoods('{{ $fs->code_item }}','{{ $fs->name }}','{{ $fs->category }}','{{ $stock->qty }}')">
                        <div class="card" id="items">
                            <!-- Photo -->
                            @if ($fs->picture)
                                <div class="img-responsive img-responsive-21x9 card-img-top"
                                    style="background-image: url(data:image/{{ Str::afterLast($fs->picture->file_name, '.') }};base64,{{ $fs->picture->blob }})">
                                </div>
                            @else
                                <div class="img-responsive img-responsive-21x9 card-img-top"
                                    style="background-image: url({{ asset('assets/image/nocontent.jpg') }})">
                                </div>
                            @endif
                            <div class="card-body">
                                <h3 class="card-title">{{ $fs->name }} ({{ $stock->qty }} Pcs)</h3>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
