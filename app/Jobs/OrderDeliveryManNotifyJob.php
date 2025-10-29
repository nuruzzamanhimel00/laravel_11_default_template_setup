<?php

namespace App\Jobs;

use App\Mail\OrderDeliveryManNotifyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderDeliveryManNotifyJob implements ShouldQueue
{
    use Queueable;
    public $tries = 5;

    public $maxExceptions = 4;

    public $timeout = 600;
    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = $this->data;

        try {
            //code...
            Mail::to($this->data['to_mail'])->send(new OrderDeliveryManNotifyMail($data));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
