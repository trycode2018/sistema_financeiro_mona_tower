<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PaymentConfirmed extends Notification
{
    public function __construct(public $payment)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Pagamento confirmado',
            'message' => 'Pagamento da fatura #' . $this->payment->invoice_id . ' foi confirmado.',
            'amount' => $this->payment->amount,
            'payment_id' => $this->payment->id,
            'invoice_id' => $this->payment->invoice_id,
        ];
    }
}