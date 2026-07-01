<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class InvoiceCreate extends Notification
{
    public function __construct(public $invoice)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Fatura criada',
            'message' => 'Uma nova fatura foi criada para o estudante #' . $this->invoice->student_id . '.',
            'amount' => $this->invoice->total_amount,
            'payment_id' => null,
            'invoice_id' => $this->invoice->id,
        ];
    }
}