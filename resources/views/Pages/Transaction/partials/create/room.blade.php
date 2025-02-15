<div class="datagrid">
    <div class="datagrid-item">
        <div class="datagrid-title">Kategori</div>
        <div class="datagrid-content">{{ $room->category->name }}</div>
    </div>
    <div class="datagrid-item">
        <div class="datagrid-title">Nomor Kamar</div>
        <div class="datagrid-content">{{ $room->number_room }}</div>
    </div>
    <div class="datagrid-item">
        <div class="datagrid-title">Harga Kamar</div>
        @if (collect($room->category->prices)->count() > 0)
            <div class="datagrid g-0">
                @foreach ($room->category->prices as $price)
                    <div class="datagrid-item m-0 g-0 p-0">
                        @switch($price->type)
                            @case('daily')
                                <div class="datagrid-title">Harian</div>
                                <div class="datagrid-content">
                                    {{ Number::currency($price->price, 'Rp.') }}</div>
                            @break

                            @case('weekly')
                                <div class="datagrid-title">Mingguan</div>
                                <div class="datagrid-content">
                                    {{ Number::currency($price->price, 'Rp.') }}</div>
                            @break

                            @case('yearly')
                                <div class="datagrid-title">Tahunan</div>
                                <div class="datagrid-content">
                                    {{ Number::currency($price->price, 'Rp.') }}</div>
                            @break

                            @default
                                <div class="datagrid-title">Bulanan</div>
                                <div class="datagrid-content">
                                    {{ Number::currency($price->price, 'Rp.') }}</div>
                        @endswitch
                    </div>
                @endforeach
            </div>
        @else
            <div class="datagrid-content">{{ $room->number_room }}</div>
        @endif
    </div>
</div>
