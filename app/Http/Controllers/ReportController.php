<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function financial(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Estatísticas principais
        $totalRevenue = Payment::whereBetween('payment_date', [$startDate, $endDate])->sum('amount');
        $totalPayments = Payment::whereBetween('payment_date', [$startDate, $endDate])->count();
        $totalInvoices = Invoice::whereBetween('issue_date', [$startDate, $endDate])->count();
        
        $paidInvoices = Invoice::whereBetween('issue_date', [$startDate, $endDate])
            ->where('status', 'paid')->count();
        $paymentRate = $totalInvoices > 0 ? ($paidInvoices / $totalInvoices) * 100 : 0;

        // Dados para gráfico de receita mensal
        $revenueData = Payment::select(
            DB::raw('YEAR(payment_date) as year'),
            DB::raw('MONTH(payment_date) as month'),
            DB::raw('SUM(amount) as total')
        )
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        $revenueChart = [
            'labels' => [],
            'data' => []
        ];

        foreach ($revenueData as $data) {
            $revenueChart['labels'][] = Carbon::create($data->year, $data->month)->translatedFormat('M Y');
            $revenueChart['data'][] = (float) $data->total;
        }

        // Dados para gráfico de métodos de pagamento
        $paymentMethodData = Payment::select('payment_method', DB::raw('COUNT(*) as count'))
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        $paymentMethodChart = [
            'labels' => [],
            'data' => []
        ];

        foreach ($paymentMethodData as $data) {
            $label = match($data->payment_method) {
                'cash' => 'Dinheiro',
                'bank_transfer' => 'Transferência',
                'card' => 'Cartão',
                'mobile_money' => 'Mobile Money',
                default => $data->payment_method
            };
            $paymentMethodChart['labels'][] = $label;
            $paymentMethodChart['data'][] = $data->count;
        }

        // Pagamentos recentes
        $recentPayments = Payment::with(['invoice.student'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();

        $students = Student::all();

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
            'endDate'
        ));
    }

    public function students(Request $request)
    {
        $academicYear = $request->academic_year ?? date('Y');
        
        $students = Student::with(['invoices', 'invoices.payments'])
            ->when($request->class, function($query, $class) {
                return $query->where('class', $class);
            })
            ->when($academicYear, function($query, $year) {
                return $query->where('academic_year', $year);
            })
            ->get();

        // Estatísticas de estudantes
        $totalStudents = $students->count();
        $studentsWithPendingPayments = $students->filter(function($student) {
            return $student->invoices->where('status', 'pending')->count() > 0;
        })->count();

        $totalDebt = $students->sum(function($student) {
            return $student->invoices->whereIn('status', ['pending', 'overdue'])->sum('balance');
        });

        $classes = Student::distinct()->pluck('class');
        $academicYears = Student::distinct()->pluck('academic_year');

        return view('reports.students', compact(
            'students',
            'totalStudents',
            'studentsWithPendingPayments',
            'totalDebt',
            'classes',
            'academicYears',
            'academicYear'
        ));
    }

    public function invoices(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $invoices = Invoice::with(['student', 'payments'])
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('issue_date', 'desc')
            ->get();

        // Estatísticas de faturas
        $totalInvoices = $invoices->count();
        $totalBilled = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('amount_paid');
        $totalPending = $invoices->where('status', 'pending')->sum('balance');
        $totalOverdue = $invoices->where('status', 'overdue')->sum('balance');

        $statusDistribution = [
            'paid' => $invoices->where('status', 'paid')->count(),
            'pending' => $invoices->where('status', 'pending')->count(),
            'overdue' => $invoices->where('status', 'overdue')->count(),
        ];

        return view('reports.invoices', compact(
            'invoices',
            'totalInvoices',
            'totalBilled',
            'totalPaid',
            'totalPending',
            'totalOverdue',
            'statusDistribution',
            'startDate',
            'endDate'
        ));
    }
}