@push('mystyles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/tabler/libs/dropzone/dist/dropzone.css') }}?{{ rand() }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/choicesjs/styles/choices.min.css') }}?{{ rand() }}">
@endpush
<div class="card">
    <div class="card-header">
        <div class="card-title">Kategori</div>
        <div class="card-actions">
            <button class="btn btn-primary" id="btn-add-category">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                Tambah Data
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                @include('Pages.Master.Category.partials.tables.categories')
            </div>
        </div>
    </div>
</div>
@include('Pages.Master.Category.partials.modals.categories')
@include('Pages.Master.Category.partials.modals.uploadPicture')
@include('Pages.Master.Category.partials.modals.viewPicture')
@push('myscript')
    <script src="{{ asset('assets/vendor/tabler/libs/dropzone/dist/dropzone-min.js') }}?{{ rand() }}"></script>
    <script src="{{ asset('assets/vendor/choicesjs/scripts/choices.min.js') }}?{{ rand() }}"></script>
    <script src="{{ asset('assets/js/Pages/Master/Category/app.js') }}?{{ rand() }}"></script>
@endpush
