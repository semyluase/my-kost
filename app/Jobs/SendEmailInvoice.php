<?php

namespace App\Jobs;

use App\Mail\Invoice\InvoiceRoomMail;
use App\Models\Email;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendEmailInvoice
{
    use Dispatchable;

    public $data;
    public $dataRent;
    /**
     * Create a new job instance.
     */
    public function __construct($data, $dataRent)
    {
        $this->data = $data;
        $this->dataRent = $dataRent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->data->to)->queue(new InvoiceRoomMail($this->data, $this->dataRent));

        Email::find($this->data->id)->update([
            'is_send'   =>  true
        ]);

        // unlink($this->data->attachment);
    }
}
