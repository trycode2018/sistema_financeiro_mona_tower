<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PaymentRejected extends Notification
{
    public function __construct(public $payment, public $invoice)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'          => 'Pagamento rejeitado',
            'payment_id'     => $this->payment->id,
            // Use a variável $this->invoice diretamente em vez de $this->payment->invoice
            'invoice_number' => $this->invoice->invoice_number, 
            'amount'         => $this->payment->amount,
            'student_name'   => $this->invoice->student->name,
            'message'        => "Pagamento rejeitado para a fatura #" . $this->invoice->invoice_number,
        ];
    }
}