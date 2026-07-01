<?php

namespace App\Services;

use App\Models\Configuracao;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function processarCobrancaEmMassa()
    {
        $estudantes = Student::with('services')->get();
        $contagem = 0;
        DB::transaction(function () use ($estudantes, &$contagem) {
            foreach ($estudantes as $estudante) {
                $fatura = $this->gerarFaturaAluno($estudante);
                if ($fatura) {
                    $contagem++;
                }
            }
        });
        return $contagem;
    }

    public function gerarFaturaAluno($student)
    {
        // 1. Calcular o total primeiro
        $mensalidade = 79000;
        $total = $mensalidade;

        // Soma dos serviços ativos mensais
        foreach ($student->services as $service) {
            if ($service->billing_type === 'monthly' && $service->is_active) {
                $total += $service->price;
            }
        }

        // 2. Se total <= 0, NÃO cria fatura (evita status 'pago' indevido)
        if ($total <= 0) {
            // Opcional: log ou retorno null
            return null;
        }

        // 3. Criar invoice já com total_amount correto
        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . Str::upper(Str::random(8)),
            'student_id' => $student->id,
            'due_date' => now()->day(1)->addMonth(),
            'issue_date' => now(),
            'description' => 'Mensalidade de ' . now()->translatedFormat('F Y'),
            'status' => 'pendente',
            'amount_paid' => 0,
            'total_amount' => $total, // já com o valor final
        ]);

        // 4. Adicionar os itens (mensalidade + serviços)
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Mensalidade Escolar',
            'amount' => $mensalidade,
            'type' => 'mensalidade',
        ]);

        foreach ($student->services as $service) {
            if ($service->billing_type === 'monthly' && $service->is_active) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $service->name,
                    'amount' => $service->price,
                    'type' => 'servico',
                ]);
            }
        }

        return $invoice;
    }

    public function isCobrancaAtiva()
    {
        return Configuracao::where('chave', 'cobranca_massa_status')
            ->value('valor') === '1';
    }

    public function alternarStatusCobranca($status)
    {
        Configuracao::updateOrCreate(
            ['chave' => 'cobranca_massa_status'],
            ['valor' => $status ? '1' : '0']
        );
    }
}