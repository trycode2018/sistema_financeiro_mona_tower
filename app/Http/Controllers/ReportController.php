<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  VIEWS (dashboard interno)
    // ─────────────────────────────────────────────────────────────

    public function financial(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfMonth();

        $totalRevenue  = Payment::where('status', 'confirmed')->whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
        $totalPayments = Payment::where('status', 'confirmed')->whereBetween('payment_date', [$startDate, $endDate])->count();
        $totalInvoices = Invoice::whereBetween('issue_date', [$startDate, $endDate])->count();

        // Cálculo da taxa de pagamento baseado em valores monetários (resolve o problema das faturas "parcial")
        $totalBilled = Invoice::whereBetween('issue_date', [$startDate, $endDate])->sum('total_amount');
        $totalPaid   = Invoice::whereBetween('issue_date', [$startDate, $endDate])->sum('amount_paid');
        $paymentRate = $totalBilled > 0 ? ($totalPaid / $totalBilled) * 100 : 0;

        // Dados para o gráfico de receita por mês (valores pagos confirmados)
        $revenueData = Payment::select(
            DB::raw('YEAR(payment_date) as year'),
            DB::raw('MONTH(payment_date) as month'),
            DB::raw('SUM(amount) as total')
        )
        ->where('status', 'confirmed')
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->groupBy('year', 'month')
        ->orderBy('year')->orderBy('month')
        ->get();

        $revenueChart = ['labels' => [], 'data' => []];
        foreach ($revenueData as $data) {
            $revenueChart['labels'][] = Carbon::create($data->year, $data->month)->translatedFormat('M Y');
            $revenueChart['data'][]   = (float) $data->total;
        }

        // Distribuição por método de pagamento (quantidade de transações)
        $paymentMethodData = Payment::select('payment_method', DB::raw('COUNT(*) as count'))
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('payment_method')->get();

        $paymentMethodChart = ['labels' => [], 'data' => []];
        foreach ($paymentMethodData as $data) {
            $paymentMethodChart['labels'][] = $this->translatePaymentMethod($data->payment_method);
            $paymentMethodChart['data'][]   = $data->count;
        }

        // Últimos pagamentos (limitado a 10)
        $recentPayments = Payment::with(['invoice.student'])
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')->limit(10)->get();

        $students = Student::all();

        \Log::info('Debug Taxa Pagamento', [
            'totalBilled' => $totalBilled ?? 0,
            'totalPaid' => $totalPaid ?? 0,
            'paymentRate' => $paymentRate ?? 0,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        return view('reports.financial', compact(
            'totalRevenue',
            'totalPayments',
            'totalInvoices',
            'paymentRate',
            'revenueChart',
            'paymentMethodChart',
            'recentPayments',
            'students',
            'startDate',
            'endDate',
            'totalBilled',
            'totalPaid'
        ));
    }

    public function students(Request $request)
    {
        $academicYear = $request->academic_year ?? date('Y');

        $students = Student::with(['invoices', 'invoices.payments'])
            ->when($request->class,         fn($q, $v) => $q->where('class', $v))
            ->when($academicYear,            fn($q, $v) => $q->where('academic_year', $v))
            ->get();

        $totalStudents               = $students->count();
        $studentsWithPendingPayments = $students->filter(fn($s) =>
            $s->invoices->whereIn('status', ['pendente', 'vencido'])->sum('balance') > 0
        )->count();

        $totalDebt = $students->sum(fn($s) =>
            $s->invoices->whereIn('status', ['pendente', 'vencido'])->sum('balance')
        );

        $classes       = Student::distinct()->pluck('class');
        $academicYears = Student::distinct()->pluck('academic_year');

        return view('reports.students', compact(
            'students', 'totalStudents', 'studentsWithPendingPayments',
            'totalDebt', 'classes', 'academicYears', 'academicYear'
        ));
    }

    public function invoices(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfMonth();

        $invoices = Invoice::with(['student', 'payments'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderBy('issue_date', 'desc')->get();

        $totalInvoices = $invoices->count();
        $totalBilled   = $invoices->sum('total_amount');
        $totalPaid     = $invoices->sum('amount_paid');
        $totalPending  = $invoices->where('status', 'pendente')->sum('balance');
        $totalOverdue  = $invoices->where('status', 'vencido')->sum('balance');

        $statusDistribution = [
            'pago'    => $invoices->where('status', 'pago')->count(),
            'pendente' => $invoices->where('status', 'pendente')->count(),
            'vencido' => $invoices->where('status', 'vencido')->count(),
            'parcial' => $invoices->where('status', 'parcial')->count(),
        ];

        return view('reports.invoices', compact(
            'invoices', 'totalInvoices', 'totalBilled', 'totalPaid',
            'totalPending', 'totalOverdue', 'statusDistribution', 'startDate', 'endDate'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    //  EXPORTAÇÕES PROFISSIONAIS
    // ─────────────────────────────────────────────────────────────

    public function exportStudents(Request $request)
    {
        $academicYear = $request->academic_year ?? date('Y');

        $students = Student::with(['invoices'])
            ->when($request->class,    fn($q, $v) => $q->where('class', $v))
            ->when($academicYear,       fn($q, $v) => $q->where('academic_year', $v))
            ->orderBy('name')
            ->get();

        $totalStudents               = $students->count();
        $studentsWithPendingPayments = $students->filter(fn($s) =>
            $s->invoices->whereIn('status', ['pendente', 'vencido'])->sum('balance') > 0
        )->count();
        $totalDebt = $students->sum(fn($s) =>
            $s->invoices->whereIn('status', ['pendente', 'vencido'])->sum('balance')
        );

        $classes         = $students->groupBy('class');
        $classLabels     = [];
        $classData       = [];
        $classPendingData = [];

        foreach ($classes as $class => $classStudents) {
            $classLabels[]      = $class;
            $classData[]        = $classStudents->count();
            $classPendingData[] = $classStudents->filter(fn($s) =>
                $s->invoices->whereIn('status', ['pendente', 'vencido'])->sum('balance') > 0
            )->count();
        }

        $filters = [];
        if ($request->filled('class'))         $filters[] = ['label' => 'Turma',        'value' => $request->class];
        if ($request->filled('academic_year')) $filters[] = ['label' => 'Ano Lectivo',  'value' => $request->academic_year];

        $html = $this->renderStudentReport(
            $totalStudents, $studentsWithPendingPayments, $totalDebt,
            $students, $classLabels, $classData, $classPendingData, $filters
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['html' => $html]);
        }

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function exportInvoices(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfMonth();

        $invoices = Invoice::with(['student', 'payments'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderBy('issue_date', 'desc')
            ->get();

        $totalInvoices  = $invoices->count();
        $totalBilled    = $invoices->sum('total_amount');
        $totalPaid      = $invoices->sum('amount_paid');
        $totalPending   = $invoices->where('status', 'pendente')->sum('balance');
        $totalOverdue   = $invoices->where('status', 'vencido')->sum('balance');
        $collectionRate = $totalBilled > 0 ? ($totalPaid / $totalBilled) * 100 : 0;

        $statusCounts = [
            'pago'    => $invoices->where('status', 'pago')->count(),
            'pendente' => $invoices->where('status', 'pendente')->count(),
            'vencido' => $invoices->where('status', 'vencido')->count(),
            'parcial' => $invoices->where('status', 'parcial')->count(),
        ];

        // Dados mensais incluindo o status 'parcial'
        $monthlyStatus = Invoice::selectRaw("DATE_FORMAT(issue_date, '%Y-%m') as month, status, COUNT(*) as count")
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->groupBy('month', 'status')->orderBy('month')->get()->groupBy('month');

        $months = $paidData = $pendingData = $overdueData = $partialData = [];
        $monthNames = ['01'=>'Jan','02'=>'Fev','03'=>'Mar','04'=>'Abr','05'=>'Mai','06'=>'Jun',
                       '07'=>'Jul','08'=>'Ago','09'=>'Set','10'=>'Out','11'=>'Nov','12'=>'Dez'];

        foreach ($monthlyStatus as $month => $statuses) {
            [$year, $m] = explode('-', $month);
            $months[]      = ($monthNames[$m] ?? $m) . '/' . substr($year, 2);
            $paidData[]    = $statuses->where('status', 'pago')->first()->count    ?? 0;
            $pendingData[] = $statuses->where('status', 'pendente')->first()->count ?? 0;
            $overdueData[] = $statuses->where('status', 'vencido')->first()->count  ?? 0;
            $partialData[] = $statuses->where('status', 'parcial')->first()->count  ?? 0; // ✅ novo
        }

        $filters = [['label' => 'Período', 'value' => $startDate->format('d/m/Y') . ' a ' . $endDate->format('d/m/Y')]];
        if ($request->filled('status')) {
            $filters[] = ['label' => 'Status', 'value' => $this->translateStatus($request->status)];
        }

        $html = $this->renderInvoiceReport(
            $totalInvoices, $totalBilled, $totalPaid, $totalPending,
            $totalOverdue, $collectionRate, $invoices, $statusCounts,
            $months, $paidData, $pendingData, $overdueData, $partialData, $filters
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['html' => $html]);
        }

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function exportFinancial(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfMonth();

        $paymentsQuery = Payment::with(['invoice.student'])
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$startDate, $endDate]);

        if ($request->filled('student_id')) {
            $paymentsQuery->whereHas('invoice', fn($q) => $q->where('student_id', $request->student_id));
        }

        $payments      = $paymentsQuery->orderBy('payment_date', 'desc')->get();
        $recentPayments = $payments->take(25);

        $totalRevenue  = $payments->sum('amount');
        $totalPayments = $payments->count();

        // Faturas emitidas no período
        $invoicesQuery = Invoice::whereBetween('issue_date', [$startDate, $endDate]);
        if ($request->filled('student_id')) {
            $invoicesQuery->where('student_id', $request->student_id);
        }
        $totalInvoices = $invoicesQuery->count();

        // Taxa de pagamento baseada em valores (corrige o problema das faturas "parcial")
        $totalBilled = $invoicesQuery->sum('total_amount');
        $totalPaid   = $invoicesQuery->sum('amount_paid');
        $paymentRate = $totalBilled > 0 ? ($totalPaid / $totalBilled) * 100 : 0;

        // Dados para o gráfico de receita mensal
        $revenueRows = Payment::select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->when($request->filled('student_id'), fn($q) =>
                $q->whereHas('invoice', fn($q2) => $q2->where('student_id', $request->student_id))
            )
            ->groupBy('year', 'month')->orderBy('year')->orderBy('month')
            ->get();

        $revenueLabels = $revenueData = [];
        foreach ($revenueRows as $row) {
            $revenueLabels[] = Carbon::create($row->year, $row->month)->translatedFormat('M Y');
            $revenueData[]   = (float) $row->total;
        }

        // Gráfico de métodos de pagamento (por valor)
        $methodColors = ['#f97316','#1a3a6b','#fb923c','#2563eb','#fdba74','#3b82f6'];
        $methodLabels = $methodAmounts = [];
        foreach ($payments->groupBy('payment_method') as $method => $group) {
            $methodLabels[]   = $this->translatePaymentMethod($method);
            $methodAmounts[] = $group->sum('amount');
        }

        // Filtros aplicados
        $filters = [['label' => 'Período', 'value' => $startDate->format('d/m/Y') . ' a ' . $endDate->format('d/m/Y')]];
        if ($request->filled('student_id')) {
            $student = Student::find($request->student_id);
            $filters[] = ['label' => 'Estudante', 'value' => $student?->name ?? 'N/A'];
        }

        // Monta o HTML do relatório
        $html = $this->renderFinancialReport(
            $totalRevenue, $totalPayments, $totalInvoices, $paymentRate,
            $recentPayments, $revenueLabels, $revenueData,
            $methodLabels, $methodAmounts, $methodColors, $filters
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['html' => $html]);
        }

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    // ─────────────────────────────────────────────────────────────
    //  RENDERIZADORES DE HTML PROFISSIONAL
    // ─────────────────────────────────────────────────────────────

    private function renderStudentReport(
        $totalStudents, $studentsWithPendingPayments, $totalDebt,
        $students, $classLabels, $classData, $classPendingData, $filters
    ): string {
        $filtersHtml = $this->buildFiltersHtml($filters, 'Nenhum filtro aplicado');

        $tableRows = '';
        $i = 0;
        foreach ($students as $student) {
            $i++;
            $totalPaid      = $student->invoices->sum('amount_paid');
            $pendingBalance = $student->invoices->whereIn('status', ['pendente', 'vencido'])->sum('balance');
            $invoiceCount   = $student->invoices->count();
            $statusClass    = $pendingBalance > 0 ? 'row-warning' : 'row-ok';

            $tableRows .= "
            <tr class='{$statusClass}'>
                <td class='td-center muted'>{$i}</td>
                <td class='td-name'><span class='student-name'>{$student->name}</span></td>
                <td>{$student->email}</td>
                <td class='td-center'><span class='badge-class'>{$student->class}</span></td>
                <td class='td-center'>{$invoiceCount}</td>
                <td class='td-right text-green'>Kz " . $this->fmt($totalPaid) . "</td>
                <td class='td-right " . ($pendingBalance > 0 ? 'text-red' : 'text-muted') . "'>Kz " . $this->fmt($pendingBalance) . "</td>
            </tr>";
        }

        $jsClassLabels      = json_encode($classLabels);
        $jsClassData        = json_encode($classData);
        $jsClassPendingData = json_encode($classPendingData);

        $generatedAt = Carbon::now()->format('d/m/Y \à\s H:i');

        return $this->wrapReport(
            title:    'RELATÓRIO DE ESTUDANTES',
            subtitle: 'Situação Financeira por Aluno',
            accentColor: '#1a3a6b',
            accentLight: '#fff7ed',
            content: "
            {$filtersHtml}

            <div class='kpi-grid kpi-3'>
                <div class='kpi-card'>
                    <p class='kpi-label'>Total de Estudantes</p>
                    <p class='kpi-value' style='color:#1a3a6b'>{$totalStudents}</p>
                </div>
                <div class='kpi-card kpi-warn'>
                    <p class='kpi-label'>Com Pagamentos Pendentes</p>
                    <p class='kpi-value' style='color:#b45309'>{$studentsWithPendingPayments}</p>
                    <p class='kpi-sub'>" . ($totalStudents > 0 ? $this->fmtPct($studentsWithPendingPayments / $totalStudents * 100) : '0%') . " do total</p>
                </div>
                <div class='kpi-card kpi-danger'>
                    <p class='kpi-label'>Dívida Total em Aberto</p>
                    <p class='kpi-value' style='color:#9b1c1c'>Kz {$this->fmt($totalDebt)}</p>
                </div>
            </div>

            <div class='charts-row'>
                <div class='chart-box'>
                    <p class='chart-title'>Estudantes por Turma</p>
                    <canvas id='classChart' height='220'></canvas>
                </div>
                <div class='chart-box'>
                    <p class='chart-title'>Pendências por Turma</p>
                    <canvas id='pendingChart' height='220'></canvas>
                </div>
            </div>

            <div class='table-box'>
                <p class='section-title'>Lista Detalhada de Estudantes</p>
                <table>
                    <thead>
                        <tr>
                            <th class='td-center' style='width:40px'>#</th>
                            <th>Estudante</th>
                            <th>E-mail</th>
                            <th class='td-center'>Turma</th>
                            <th class='td-center'>Faturas</th>
                            <th class='td-right'>Total Pago</th>
                            <th class='td-right'>Saldo Pendente</th>
                        </tr>
                    </thead>
                    <tbody>{$tableRows}</tbody>
                </table>
            </div>",
            scripts: "
            new Chart(document.getElementById('classChart'), {
                type: 'bar',
                data: {
                    labels: {$jsClassLabels},
                    datasets: [{
                        label: 'Estudantes',
                        data: {$jsClassData},
                        backgroundColor: 'rgba(26,58,107,0.75)',
                        borderColor: 'rgba(26,58,107,1)',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
            new Chart(document.getElementById('pendingChart'), {
                type: 'doughnut',
                data: {
                    labels: {$jsClassLabels},
                    datasets: [{
                        data: {$jsClassPendingData},
                        backgroundColor: ['#dc2626','#f59e0b','#8b5cf6','#ec4899','#06b6d4','#84cc16','#f97316']
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });",
            generatedAt: $generatedAt
        );
    }

    private function renderInvoiceReport(
        $totalInvoices, $totalBilled, $totalPaid, $totalPending, $totalOverdue,
        $collectionRate, $invoices, $statusCounts,
        $months, $paidData, $pendingData, $overdueData, $partialData, $filters
    ): string {
        $filtersHtml = $this->buildFiltersHtml($filters);

        $tableRows = '';
        $i = 0;
        foreach ($invoices as $invoice) {
            $i++;
            $statusBadge = match($invoice->status) {
                'pago'    => "<span class='badge badge-paid'>Paga</span>",
                'pendente'=> "<span class='badge badge-pending'>Pendente</span>",
                'vencido' => "<span class='badge badge-overdue'>Vencida</span>",
                'parcial' => "<span class='badge badge-partial'>Parcial</span>",
                default   => "<span class='badge'>{$invoice->status}</span>",
            };
            $rowClass = match($invoice->status) {
                'vencido' => 'row-warning',
                default   => '',
            };
            $tableRows .= "
            <tr class='{$rowClass}'>
                <td class='td-center muted'>{$i}</td>
                <td><span class='invoice-num'>#{$invoice->invoice_number}</span></td>
                <td>{$invoice->student->name}</td>
                <td class='td-center'>{$invoice->due_date->format('d/m/Y')}</td>
                <td class='td-center'>{$statusBadge}</td>
                <td class='td-right'>Kz " . $this->fmt($invoice->total_amount) . "</td>
                <td class='td-right text-green'>Kz " . $this->fmt($invoice->amount_paid) . "</td>
                <td class='td-right " . ($invoice->balance > 0 ? 'text-red' : 'text-muted') . "'>Kz " . $this->fmt($invoice->balance) . "</td>
            </tr>";
        }

        $jsMonths      = json_encode($months);
        $jsPaidData    = json_encode($paidData);
        $jsPendingData = json_encode($pendingData);
        $jsOverdueData = json_encode($overdueData);
        $jsPartialData = json_encode($partialData);
        $generatedAt   = Carbon::now()->format('d/m/Y \à\s H:i');

        return $this->wrapReport(
            title:    'RELATÓRIO DE FATURAS',
            subtitle: 'Análise de Cobranças e Recebimentos',
            accentColor: '#1a3a6b',
            accentLight: '#fff7ed',
            content: "
            {$filtersHtml}

            <div class='kpi-grid kpi-4'>
                <div class='kpi-card'>
                    <p class='kpi-label'>Total de Faturas</p>
                    <p class='kpi-value' style='color:#1a3a6b'>{$totalInvoices}</p>
                    <p class='kpi-sub'>Pagas: {$statusCounts['pago']} &nbsp;|&nbsp; Pendentes: {$statusCounts['pendente']} &nbsp;|&nbsp; Vencidas: {$statusCounts['vencido']} &nbsp;|&nbsp; Parciais: {$statusCounts['parcial']}</p>
                </div>
                <div class='kpi-card'>
                    <p class='kpi-label'>Valor Total Emitido</p>
                    <p class='kpi-value' style='color:#1a3a6b'>Kz {$this->fmt($totalBilled)}</p>
                </div>
                <div class='kpi-card kpi-warn'>
                    <p class='kpi-label'>Valor Pendente</p>
                    <p class='kpi-value' style='color:#b45309'>Kz {$this->fmt($totalPending)}</p>
                </div>
                <div class='kpi-card kpi-danger'>
                    <p class='kpi-label'>Valor Vencido</p>
                    <p class='kpi-value' style='color:#9b1c1c'>Kz {$this->fmt($totalOverdue)}</p>
                </div>
            </div>

            <div class='summary-box'>
                <div class='summary-row'>
                    <span class='summary-label'>Valor Total Recebido:</span>
                    <span class='summary-value text-green'>Kz {$this->fmt($totalPaid)}</span>
                </div>
                <div class='summary-row'>
                    <span class='summary-label'>Valor Total Emitido:</span>
                    <span class='summary-value'>Kz {$this->fmt($totalBilled)}</span>
                </div>
                <div class='summary-row summary-total'>
                    <span>Taxa de Cobrança</span>
                    <span class='text-green'>{$this->fmtPct($collectionRate)}</span>
                </div>
            </div>

            <div class='charts-row'>
                <div class='chart-box'>
                    <p class='chart-title'>Distribuição por Status</p>
                    <canvas id='statusChart' height='220'></canvas>
                </div>
                <div class='chart-box'>
                    <p class='chart-title'>Evolução Mensal por Status</p>
                    <canvas id='trendChart' height='220'></canvas>
                </div>
            </div>

            <div class='table-box'>
                <p class='section-title'>Listagem Completa de Faturas</p>
                <table>
                    <thead>
                        <tr>
                            <th class='td-center' style='width:40px'>#</th>
                            <th>Fatura</th>
                            <th>Estudante</th>
                            <th class='td-center'>Vencimento</th>
                            <th class='td-center'>Status</th>
                            <th class='td-right'>Valor Total</th>
                            <th class='td-right'>Valor Pago</th>
                            <th class='td-right'>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>{$tableRows}</tbody>
                </table>
            </div>",
            scripts: "
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Pagas','Pendentes','Vencidas','Parciais'],
                    datasets: [{ data: [{$statusCounts['pago']},{$statusCounts['pendente']},{$statusCounts['vencido']},{$statusCounts['parcial']}], backgroundColor: ['#16a34a','#f59e0b','#dc2626','#8b5cf6'] }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: {$jsMonths},
                    datasets: [
                        { label:'Pagas',    data:{$jsPaidData},    borderColor:'#16a34a', backgroundColor:'rgba(22,163,74,0.1)',   tension:0.4, fill:true },
                        { label:'Pendentes',data:{$jsPendingData}, borderColor:'#f59e0b', backgroundColor:'rgba(245,158,11,0.1)',  tension:0.4, fill:true },
                        { label:'Vencidas', data:{$jsOverdueData}, borderColor:'#dc2626', backgroundColor:'rgba(220,38,38,0.1)',   tension:0.4, fill:true },
                        { label:'Parciais', data:{$jsPartialData}, borderColor:'#8b5cf6', backgroundColor:'rgba(139,92,246,0.1)',   tension:0.4, fill:true }
                    ]
                },
                options: { responsive: true, plugins:{ legend:{ position:'bottom' } }, scales:{ y:{ beginAtZero:true } } }
            });",
            generatedAt: $generatedAt
        );
    }

    private function renderFinancialReport(
        $totalRevenue, $totalPayments, $totalInvoices, $paymentRate,
        $recentPayments, $revenueLabels, $revenueData,
        $methodLabels, $methodData, $methodColors, $filters
    ): string {
        $filtersHtml = $this->buildFiltersHtml($filters);

        $tableRows = '';
        $i = 0;
        foreach ($recentPayments as $payment) {
            $i++;
            $tableRows .= "
            <tr>
                <td class='td-center muted'>{$i}</td>
                <td class='td-center'>{$payment->payment_date->format('d/m/Y')}</td>
                <td><strong>{$payment->invoice->student->name}</strong></td>
                <td class='td-center'><span class='invoice-num'>#{$payment->invoice->invoice_number}</span></td>
                <td class='td-center'><span class='method-pill'>" . $this->translatePaymentMethod($payment->payment_method) . "</span></td>
                <td class='td-right text-green'><strong>Kz " . $this->fmt($payment->amount) . "</strong></td>
            </tr>";
        }

        $jsRevenueLabels = json_encode($revenueLabels);
        $jsRevenueData   = json_encode($revenueData);
        $jsMethodLabels  = json_encode($methodLabels);
        $jsMethodData    = json_encode($methodData);
        $jsMethodColors  = json_encode(array_slice($methodColors, 0, count($methodLabels)));
        $generatedAt     = Carbon::now()->format('d/m/Y \à\s H:i');

        return $this->wrapReport(
            title:    'RELATÓRIO FINANCEIRO',
            subtitle: 'Receitas, Pagamentos e Indicadores de Desempenho',
            accentColor: '#1a3a6b',
            accentLight: '#fff7ed',
            content: "
            {$filtersHtml}

            <div class='kpi-grid kpi-4'>
                <div class='kpi-card kpi-highlight'>
                    <p class='kpi-label'>Receita Total</p>
                    <p class='kpi-value' style='color:#f97316'>Kz {$this->fmt($totalRevenue)}</p>
                    <p class='kpi-sub'>Pagamentos confirmados</p>
                </div>
                <div class='kpi-card'>
                    <p class='kpi-label'>Pagamentos Confirmados</p>
                    <p class='kpi-value' style='color:#1a3a6b'>{$totalPayments}</p>
                </div>
                <div class='kpi-card'>
                    <p class='kpi-label'>Faturas Emitidas</p>
                    <p class='kpi-value' style='color:#1a3a6b'>{$totalInvoices}</p>
                </div>
                <div class='kpi-card'>
                    <p class='kpi-label'>Taxa de Pagamento</p>
                    <p class='kpi-value' style='color:#f97316'>{$this->fmtPct($paymentRate)}</p>
                    <div class='progress-bar'><div class='progress-fill' style='width:{$this->fmtPct($paymentRate)};background:#f97316'></div></div>
                </div>
            </div>

            <div class='charts-row'>
                <div class='chart-box chart-wide'>
                    <p class='chart-title'>Receita Confirmada por Período</p>
                    <canvas id='revenueChart' height='160'></canvas>
                </div>
                <div class='chart-box'>
                    <p class='chart-title'>Métodos de Pagamento</p>
                    <canvas id='methodChart' height='160'></canvas>
                </div>
            </div>

            <div class='table-box'>
                <p class='section-title'>Últimos Pagamentos Confirmados</p>
                <table>
                    <thead>
                        <tr>
                            <th class='td-center' style='width:40px'>#</th>
                            <th class='td-center'>Data</th>
                            <th>Estudante</th>
                            <th class='td-center'>Fatura</th>
                            <th class='td-center'>Método</th>
                            <th class='td-right'>Valor</th>
                        </tr>
                    </thead>
                    <tbody>{$tableRows}</tbody>
                </table>
            </div>",
            scripts: "
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: {$jsRevenueLabels},
                    datasets: [{
                        label: 'Receita confirmada (Kz)',
                        data: {$jsRevenueData},
                        backgroundColor: 'rgba(26,58,107,0.7)',
                        borderColor: 'rgba(26,58,107,1)',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
            new Chart(document.getElementById('methodChart'), {
                type: 'doughnut',
                data: {
                    labels: {$jsMethodLabels},
                    datasets: [{ data: {$jsMethodData}, backgroundColor: {$jsMethodColors} }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });",
            generatedAt: $generatedAt
        );
    }

    // ─────────────────────────────────────────────────────────────
    //  TEMPLATE BASE PROFISSIONAL
    // ─────────────────────────────────────────────────────────────

    private function wrapReport(
        string $title,
        string $subtitle,
        string $accentColor,
        string $accentLight,
        string $content,
        string $scripts,
        string $generatedAt
    ): string {
        $css = $this->reportCSS($accentColor, $accentLight);
        $logoPath = public_path('images/MonaTower.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }
        return <<<HTML
<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>{$css}</style>
</head>
<body>

    <!-- ── Botão de impressão ── -->
    <button class="btn-print no-print" onclick="window.print()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8" rx="1"/>
        </svg>
        Imprimir / Guardar PDF
    </button>

    <div class="page">

        <!-- ── Cabeçalho Institucional ── -->
        <header class="report-header">
            <div class="header-brand">
                <div class="header-logo">
                    <img src="{$logoBase64}" alt="Logo" style="width:60px; height:60px; object-fit:contain;">
                </div>
                <div class="institution-info">
                    <h2 class="institution-name">Complexo Escolar Mona Tower</h2>
                </div>
            </div>
            <div class="header-meta">
                <div class="report-generated">Gerado em: $generatedAt</div>
                <div class="report-ref">Sistema de Gestão e Processamento de Pagamentos</div>
            </div>
        </header>

        <!-- ── Título do Relatório ── -->
        <div class="report-title-block">
            <h1 class="report-title">{$title}</h1>
            <p class="report-subtitle">{$subtitle}</p>
        </div>

        <!-- ── Conteúdo ── -->
        {$content}

        <!-- ── Rodapé ── -->
        <footer class="report-footer">
            <div class="footer-left">
                <p>Documento gerado automaticamente pelo Sistema de Gestão e Processamento de Pagamentos.</p>
                <p>Este relatório é confidencial e destina-se exclusivamente ao uso interno da instituição.</p>
            </div>
            <div class="footer-right">
                <p>Página 1</p>
            </div>
        </footer>

    </div>

    <script>
        {$scripts}
    </script>

</body>
</html>
HTML;
    }

    private function reportCSS(string $accent, string $accentLight): string
    {
        return "
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #f1f5f9;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            max-width: 1100px;
            margin: 24px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 32px rgba(0,0,0,.10);
            padding: 40px 48px 32px;
        }

        /* ── Cabeçalho ── */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 24px;
        }
        .header-brand { display: flex; align-items: center; gap: 14px; }
        .header-logo {
            width: 52px; height: 52px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .institution-name { font-size: 15px; font-weight: 700; color: #0f172a; }
        .institution-sub  { font-size: 12px; color: #64748b; margin-top: 2px; }
        .header-meta { text-align: right; }
        .report-generated { font-size: 12px; color: #64748b; }
        .report-ref { font-family: 'DM Mono', monospace; font-size: 11px; color: #94a3b8; margin-top: 4px; }

        /* ── Título ── */
        .report-title-block {
            border-left: 5px solid #f97316;
            padding: 12px 18px;
            margin-bottom: 28px;
            background: #fff7ed;
            border-radius: 0 8px 8px 0;
        }
        .report-title    { font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: .5px; }
        .report-subtitle { font-size: 12px; color: #64748b; margin-top: 3px; }

        /* ── Filtros ── */
        .filters-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 24px;
            align-items: center;
        }
        .filters-label { font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-right: 4px; }
        .filter-chip {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 12px;
            color: #475569;
        }
        .filter-chip strong { color: #1e293b; }

        /* ── KPIs ── */
        .kpi-grid { display: grid; gap: 16px; margin-bottom: 28px; }
        .kpi-3 { grid-template-columns: repeat(3, 1fr); }
        .kpi-4 { grid-template-columns: repeat(4, 1fr); }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px 20px;
        }
        .kpi-card.kpi-highlight { background: #fff7ed; border-color: #fed7aa; }
        .kpi-card.kpi-warn      { background: #fffbeb; border-color: #fcd34d; }
        .kpi-card.kpi-danger    { background: #fff1f2; border-color: #fca5a5; }
        .kpi-label  { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-bottom: 6px; }
        .kpi-value  { font-size: 22px; font-weight: 700; line-height: 1.1; }
        .kpi-sub    { font-size: 11px; color: #94a3b8; margin-top: 4px; }

        /* Progress bar */
        .progress-bar  { height: 5px; background: #e2e8f0; border-radius: 4px; margin-top: 8px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 4px; }

        /* ── Summary box ── */
        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 28px;
        }
        .summary-row       { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #e2e8f0; }
        .summary-row:last-child { border-bottom: none; }
        .summary-label     { color: #64748b; font-size: 13px; }
        .summary-value     { font-weight: 600; font-size: 13px; }
        .summary-total     { font-size: 15px; font-weight: 700; padding-top: 10px; }

        /* ── Charts ── */
        .charts-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 28px; }
        .chart-wide  { grid-column: 1 / 2; }
        .chart-box   { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; }
        .chart-title { font-size: 12px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 14px; }

        /* ── Table ── */
        .table-box     { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 28px; overflow: auto; }
        .section-title { font-size: 12px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 14px; }

        table      { width: 100%; border-collapse: collapse; }
        thead tr   { background: #1a3a6b; }
        th {
            padding: 11px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: rgba(255,255,255,.9);
        }
        td {
            padding: 9px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12.5px;
            color: #374151;
        }
        tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #f1f5f9; }
        tr.row-warning td { background: #fff9f0; }
        tr.row-ok td      { }

        /* Helpers */
        .td-center  { text-align: center; }
        .td-right   { text-align: right; }
        .td-name    { font-weight: 600; }
        .muted      { color: #94a3b8; font-size: 11px; }
        .text-green { color: #166534; font-weight: 600; }
        .text-red   { color: #991b1b; font-weight: 600; }
        .text-muted { color: #94a3b8; }
        .student-name { font-weight: 600; color: #0f172a; }
        .invoice-num  { font-family: 'DM Mono', monospace; font-size: 12px; color: #475569; }

        /* Badges */
        .badge         { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-paid    { background:#dcfce7; color:#166534; }
        .badge-pending { background:#fef3c7; color:#92400e; }
        .badge-overdue { background:#fee2e2; color:#991b1b; }
        .badge-class   { display:inline-block; background:#fff7ed; color:#f97316; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .method-pill   { display:inline-block; background:#eff6ff; color:#1a3a6b; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:600; }

        /* ── Rodapé ── */
        .report-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #94a3b8;
        }
        .footer-left p + p { margin-top: 2px; }
        .footer-right { text-align: right; }
        .footer-right p + p { margin-top: 2px; }

        /* ── Botão de impressão ── */
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f97316;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(249,115,22,.35);
            z-index: 1000;
            transition: opacity .2s;
        }
        .btn-print:hover { opacity: .85; }

        /* ── Print Media ── */
        @media print {
            body { background: white; font-size: 11px; }
            .page {
                margin: 0;
                padding: 20px 28px;
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
            .no-print { display: none !important; }
            .kpi-grid { grid-template-columns: repeat(4, 1fr) !important; }
            .charts-row { grid-template-columns: 1fr 1fr !important; }
            .chart-box { page-break-inside: avoid; }
            .table-box { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }
        ";
    }

    // ─────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────

    private function buildFiltersHtml(array $filters, string $emptyText = 'Nenhum filtro aplicado'): string
    {
        if (empty($filters)) {
            return "<div class='filters-bar'><span class='filters-label'>Filtros:</span><span class='filter-chip'>{$emptyText}</span></div>";
        }

        $chips = array_map(fn($f) => "<span class='filter-chip'><strong>{$f['label']}:</strong> {$f['value']}</span>", $filters);

        return "<div class='filters-bar'><span class='filters-label'>Filtros:</span>" . implode('', $chips) . "</div>";
    }

    private function translatePaymentMethod(string $method): string
    {
        return match($method) {
            'cash'          => 'Dinheiro',
            'bank_transfer' => 'Transferência',
            'card'          => 'Cartão',
            'mobile_money'  => 'Mobile Money',
            default         => ucfirst($method),
        };
    }

    private function translateStatus(string $status): string
    {
        return match($status) {
            'pago'    => 'Pagas',
            'pendente' => 'Pendentes',
            'vencido' => 'Vencidas',
            'parcial' => 'Parciais',
            default   => ucfirst($status),
        };
    }

    private function now(): Carbon { return Carbon::now(); }
    private function fmt(float|int $value): string { return number_format($value, 2, ',', ' '); }
    private function fmtPct(float|int $value): string { return number_format($value, 1) . '%'; }
}