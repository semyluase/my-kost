<?php

namespace App\Jobs;

use App\Mail\Invoice\InvoiceOrderMail;
use App\Models\Email;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailReceipt
{
    use Dispatchable;

    public $data;
    public $dataTransaction;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $dataTransaction)
    {
        $this->data = $data;
        $this->dataTransaction = $dataTransaction;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->data->to)->queue(new InvoiceOrderMail($this->data, $this->dataTransaction));

            Email::find($this->data->id)->update([
                'is_send'   =>  true
            ]);

            // unlink($this->data->attachment);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}
