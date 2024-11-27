<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center">{{ $home->name }}</h2>
                <h4 class="text-center">{{ $home->address }}</h4>
            </div>
        </div>
        <hr>
        <div class="row mt-3">
            <div class="col-6 markdown">
                <p class="text-end">Berat</p>
            </div>
            <div class="col-6 markdown">
                <p class="text-start">
                    {{ $laundry->qty_laundry }} Kg
                </p>
            </div>
        </div>
    </div>
</div>
