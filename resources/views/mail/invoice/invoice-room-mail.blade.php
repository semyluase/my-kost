<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $data->subject }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" />
</head>

<body>
    <div class="container-xl">
        <div class="row mb-3">
            <p>Kepada {{ $dataRent->member->user->name }} yang terhormat,</p>
            <p>Semoga email ini sampai kepada Anda dengan baik.</p>
            <p>Kami ingin mengonfirmasi bahwa pembayaran Anda telah berhasil diterima dan diproses untuk pemesanan kamar
                Anda. Kami sangat menghargai kepercayaan Anda terhadap {{ $dataRent->member->user->location->name }}.
            </p>
            <p>Untuk arsip Anda, kami telah melampirkan salinan bukti pembayaran Anda yang berisi rincian lengkap
                penyewaan Anda di {{ $dataRent->member->user->location->name }}.</p>
            <p>Jika Anda memiliki pertanyaan lebih lanjut atau memerlukan klarifikasi tambahan, jangan ragu untuk
                menghubungi kami.</p>
            <p>Terima kasih telah memilih kami, dan kami berharap dapat melayani Anda lagi di masa mendatang.</p>
            <br><br><br>
            <p>Hormat kami,</p>
            <br><br>
            <p><b>{{ $dataRent->member->user->location->name }}</b></p>
        </div>
    </div>
</body>

</html>
