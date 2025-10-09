<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailReceipt as JobsSendEmailReceipt;
use App\Models\Email;
use App\Models\TransactionHeader;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class SendEmailReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-receipt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Receipt';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dataEmail = Email::where('is_send', false)
            ->where('is_order', true)
            ->get();

        $path = public_path('assets/image/Asset 1.png');
        $imageData = file_get_contents($path);
        $mimeType = mime_content_type($path);
        $base64 = base64_encode($imageData);
        $dataUri = 'data:' . $mimeType . ';base64,' . $base64;

        if ($dataEmail) {
            foreach ($dataEmail as $key => $value) {
                $dataTransaction = TransactionHeader::where('nobukti', $value->no_invoice)
                    ->first();

                $pdf = Pdf::loadView('Pages.Services.PDF.receiptEmailAttach', [
                    'data'  =>  $dataTransaction,
                    'image' =>  $dataUri,
                ]);

                $filePath = public_path('assets/invoice/' . $dataTransaction->nobukti . '.pdf');
                $pdf->save($filePath);

                JobsSendEmailReceipt::dispatch($value, $dataTransaction);
            }
        }
    }
}
