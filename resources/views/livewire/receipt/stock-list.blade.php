<div>
    <div class="modal fade {{ $showModal ? 'show' : '' }}" tabindex="-1" {{ $showModal ? "role='dialog'" : '' }}
        style="{{ $showModal ? 'display:block;' : 'display:hidden;' }}">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable border rounded border-blue"
            role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock Barang</h5>
                    <button type="button" class="btn-close" wire:click="closeModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 justify-content-end">
                        <div class="col-md-3">
                            <select wire:model.live="homeID" class="form-select"
                                {{ auth()->user()->role->slug == 'super-admin' || auth()->user()->role->slug == 'admin' ? '' : 'readonly' }}>
                                <option value="">Pilih Alamat</option>
                                @if ($homeList)
                                    @foreach ($homeList as $h)
                                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" wire:model.live="search" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama Barang</th>
                                            <th>Kategori</th>
                                            <th>Sisa Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($items as $item)
                                            <tr>
                                                <td>{{ $item->code_item }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->category ? $item->category : '-' }}</td>
                                                <td>{{ $item->qty }}</td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
