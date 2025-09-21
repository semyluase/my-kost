<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailInvoice as JobsSendEmailInvoice;
use App\Models\Deposite;
use App\Models\Email;
use App\Models\TransactionRent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class SendEmailInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dataEmail = Email::where('is_send', false)
            ->get();

        if ($dataEmail) {
            foreach ($dataEmail as $key => $value) {
                $dataRent = TransactionRent::with(['member', 'member.user', 'room', 'room.category'])->where('no_invoice', $value->no_invoice)
                    ->first();

                $deposit = Deposite::where('user_id', $dataRent->member->user->id)
                    ->where('room_id', $dataRent->room_id)
                    ->where('is_checkout', false)
                    ->first();

                $pdf = Pdf::loadView('Pages.Transaction.Pdf.invoicePdf', [
                    'data'  =>  $dataRent,
                    'deposit'  =>  $deposit
                ]);

                $filePath = public_path('assets/invoice/' . $dataRent->no_invoice . '.pdf');
                $pdf->save($filePath);

                JobsSendEmailInvoice::dispatch($value, $dataRent);
            }
        }
    }
}
