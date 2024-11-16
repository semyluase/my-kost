@php
    use Illuminate\Support\Str;
@endphp
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Barang</h3>
    </div>
    <div class="card-body overflow-scroll">
        <div class="row">
            @foreach ($foodSnacks as $fs)
                <div class="col-4">
                    <a href="javascript:;"
                        onclick="fnReceipt.onSelectGoods('{{ $fs->code_item }}','{{ $fs->name }}','{{ $fs->category }}')">
                        <div class="card">
                            <!-- Photo -->
                            <div class="img-responsive img-responsive-21x9 card-img-top"
                                style="background-image: url(data:image/{{ Str::afterLast($fs->picture->file_name, '.') }};base64,{{ $fs->picture->blob }})">
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ $fs->name }}</h3>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
