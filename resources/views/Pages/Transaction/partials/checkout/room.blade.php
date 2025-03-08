@php
    use App\Models\Room;
    use Illuminate\Support\Number;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Str;

    // dd($room);

@endphp
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
        <div class="datagrid-title">Nama Penghuni</div>
        <div class="datagrid-content">{{ $room->rent->member->user->name }}</div>
    </div>
    <div class="datagrid-item">
        <div class="datagrid-title">Tanggal Check In</div>
        <div class="datagrid-content">{{ Carbon::parse($room->rent->start_date)->isoFormat('DD MMM YYYY') }}</div>
    </div>
    <div class="datagrid-item">
        <div class="datagrid-title">Tanggal Check Out</div>
        <div class="datagrid-content">{{ Carbon::parse($room->rent->end_date)->isoFormat('DD MMM YYYY') }}</div>
    </div>
    <div class="datagrid-item">
        <div class="datagrid-title">Sisa Durasi</div>
        <div class="datagrid-content">
            {{ intval(Carbon::now('Asia/Jakarta')->diffInDays(Carbon::parse($room->rent->end_date))) }} Hari</div>
    </div>
</div>
