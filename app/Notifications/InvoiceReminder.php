<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InvoiceReminder extends Notification
{
    use Queueable;

    public function __construct(public $invoice, public $type)
    {
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {//Sistema Regards
        $today = now()->startOfDay();
        $dueDate = \Carbon\Carbon::parse($this->invoice->due_date)->startOfDay();
        $daysOverdue = $dueDate->diffInDays($today, false);
        
        if ($this->type == 'upcoming_3days') {
            $title = 'Lembrete de vencimento';
            $message = "A fatura {$this->invoice->invoice_number} vence em 3 dias. Valor: " . number_format($this->invoice->total_amount, 2, ',', '.') . " Kz";
        } else {
            $title = 'Fatura vencida';
            if ($daysOverdue <= 0) {
                $message = "A fatura {$this->invoice->invoice_number} vence hoje! Data de vencimento: " . date('d/m/Y', strtotime($this->invoice->due_date)) . ". Valor: " . number_format($this->invoice->total_amount, 2, ',', '.') . " Kz";
            } else {
                $message = "A fatura {$this->invoice->invoice_number} esta vencida ha {$daysOverdue} dia(s). Data de vencimento: " . date('d/m/Y', strtotime($this->invoice->due_date)) . ". Valor: " . number_format($this->invoice->total_amount, 2, ',', '.') . " Kz";
            }
        }

        return [
            'title' => $title,
            'message' => $message,
            'amount' => $this->invoice->total_amount,
            'payment_id' => null,
            'invoice_id' => $this->invoice->id,
            'type' => $this->type,
            'due_date' => $this->invoice->due_date,
            'invoice_number' => $this->invoice->invoice_number,
        ];
    }

    public function toMail($notifiable)
    {
        $today = now()->startOfDay();

        $dueDate = \Carbon\Carbon::parse($this->invoice->due_date)->startOfDay();
        $daysOverdue = $dueDate->diffInDays($today, false);

        $studentName = $this->invoice->student->name ?? 'Aluno';

        if ($this->type == 'upcoming_3days') {

            $subject = "Pagamento de {$this->invoice->description} de {$studentName} - Complexo Escolar Mona Tower";

            $message = "A/o {$this->invoice->description} de {$studentName} vence em 3 dias.";

        } elseif ($this->type == 'upcoming_2days') {

            $subject = "Pagamento de {$this->invoice->description} de {$studentName} - Complexo Escolar Mona Tower";

            $message = "A/o {$this->invoice->description} de {$studentName} vence em 2 dias.";

        } elseif ($this->type == 'upcoming_1day') {

            $subject = "Pagamento de {$this->invoice->description} de {$studentName} - Complexo Escolar Mona Tower";

            $message = "A/o {$this->invoice->description} de {$studentName} vence amanhã.";

        } else {

            $subject = "Pagamento de {$this->invoice->description} de {$studentName} - Complexo Escolar Mona Tower";

            if ($daysOverdue <= 0) {

                $message = "A/o {$this->invoice->description} de {$studentName} vence hoje.";

            } else {

                $message = "A/o {$this->invoice->description} de {$studentName} está vencida há {$daysOverdue} dia(s).";
            }
        }

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($subject)
            ->greeting('Olá, ' . $notifiable->name)
            ->line($message)
            ->line('Valor: ' . number_format($this->invoice->total_amount, 2, ',', '.') . ' Kz')
            ->line('Data de vencimento: ' . date('d/m/Y', strtotime($this->invoice->due_date)))
            ->salutation('© ' . date('Y') . ' Complexo Escolar Mona Tower. All rights reserved.');
    }
}