<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <style>
        header {
            position: fixed;
            top: 7px;
            width: 100%;
        }

        body {
            font-size: 11.5pt;
            margin-top: 85px;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .fs-14 {
            font-size: 14pt;
        }

        .pt-1 {
            padding-top: 1rem;
        }

        .page_break {
            page-break-before: always;
        }

        .detail_sewa {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid;
        }

        .detail_sewa tr,
        .detail_sewa tr td {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid;
        }
    </style>
</head>
@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Str;

    Carbon::setLocale('id');
@endphp

<body>
    <header>
        <div class="text-center bold fs-14">{{ $member->user->location->name }}</div>
        <div class="text-center">{{ $member->user->location->address }}</div>
        <hr>
    </header>
    <div class="text-center"><strong>Data Penghuni</strong></div>
    <div class="pt-1">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;">
                    <table>
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td>{{ $member->user ? $member->user->name : '' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Lahir</td>
                            <td>:</td>
                            <td>{{ Carbon::parse($member->dob)->isoFormat('DD MMMM YYYY') }}
                                ({{ Carbon::parse($member->dob)->age }} Tahun)</td>
                        </tr>
                        <tr>
                            <td>No. HP</td>
                            <td>:</td>
                            <td>{{ $member->user ? (Str::startsWith($member->user->phone_number, '0') ? $member->user->phone_number : '0' . $member->user->phone_number) : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td>No. Identitas</td>
                            <td>:</td>
                            <td>{{ $member->identity }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%;">
                    @if ($member->user)
                        @if ($member->user->foto)
                            <img src="{{ public_path('assets/upload/userIdentity/' . $member->user->foto->file_name) }}"
                                alt="" style="width: 50%; display:block; margin: auto;">
                        @endif
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    @if ($member->userIdentity)
                        <img src="{{ public_path('assets/upload/userIdentity/' . $member->userIdentity->file_name) }}"
                            alt="" style="width: 50%; display:block; margin: auto;">
                    @endif
                </td>
            </tr>
        </table>
    </div>
    @if (collect($member->historyRent)->count() > 0)
        <div class="page_break"></div>
        <div class="text-center"><strong>Detail Sewa</strong></div>
        <div class="pt-1">
            <table class="detail_sewa text-center">
                <thead>
                    <tr>
                        <td rowspan="2">No.</td>
                        <td rowspan="2">No. Kamar</td>
                        <td rowspan="2">Durasi</td>
                        <td rowspan="2">Tanggal Transaksi</td>
                        <td colspan="2">Tanggal</td>
                        <td rowspan="2">Status</td>
                    </tr>
                    <tr>
                        <td>Mulai Sewa</td>
                        <td>Selesai Sewa</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($member->historyRent as $rent)
                        @php
                            $rent->load(['room']);

                            switch ($rent->duration) {
                                case 'daily':
                                    $durasi = 'Harian';
                                    break;

                                case 'weekly':
                                    $durasi = 'Mingguan';
                                    break;

                                case 'monthly':
                                    $durasi = 'Bulanan';
                                    break;

                                case 'yearly':
                                    $durasi = 'Tahunan';
                                    break;

                                default:
                                    $durasi = '';
                                    break;
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rent->room ? $rent->room->number_room : '' }}</td>
                            <td>{{ $durasi }}</td>
                            <td>{{ $rent->tanggal_transaksi != null ? Carbon::parse($rent->tanggal_transaksi)->isoFormat('DD MMMM YYYY') : '' }}
                            </td>
                            <td>{{ Carbon::parse($rent->start_date)->isoFormat('DD MMMM YYYY') }}
                            </td>
                            <td>{{ Carbon::parse($rent->end_date)->isoFormat('DD MMMM YYYY') }}
                            </td>
                            <td>
                                @if ($rent->is_change_room)
                                    Pindah Kamar
                                @elseif ($rent->is_checkout_abnormal)
                                    Keluar Sebelum Waktu
                                @elseif ($rent->is_checkout_normal)
                                    Habis Masa Sewa
                                @else
                                    Sewa Baru
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>

</html>
