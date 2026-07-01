<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{//totalRevenue
    public function index()
    {
        try {
            $totalStudents = Student::count();
            $paidInvoices = Invoice::where('status', 'pago')->count();
            $partiallyPaidInvoices = Invoice::where('status', 'parcial')->count();
            $pendingInvoices = Invoice::where('status', 'pendente')->count();
            $overdueInvoices = Invoice::where('status', 'vencido')->count();
            $totalUsers = User::count();
            $totalRevenue = Payment::where('status', 'confirmed')->sum('amount');
            $totalBilled = Invoice::sum('total_amount');
            $totalPaid   = Invoice::sum('amount_paid');
            $paymentRate = $totalBilled > 0 ? ($totalPaid / $totalBilled) * 100 : 0;

            // Revenue data for the last 6 months
            $revenueData = Payment::select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'confirmed')
            ->where('payment_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

            $revenueChart = [
                'labels' => [],
                'data' => []
            ];

            // Generate labels for the last 6 months if no data exists
            if ($revenueData->isEmpty()) {
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $revenueChart['labels'][] = $date->translatedFormat('M Y');
                    $revenueChart['data'][] = 0;
                }
            } else {
                foreach ($revenueData->reverse() as $data) {
                    $revenueChart['labels'][] = date('M Y', mktime(0, 0, 0, $data->month, 1, $data->year));
                    $revenueChart['data'][] = (float) $data->total;
                }
            }

            $paymentChartData = [
                (int) $paidInvoices,
                (int) $partiallyPaidInvoices,
                (int) $pendingInvoices,
                (int) $overdueInvoices,
            ];

            $recentPayments = Payment::with(['invoice.student'])
                ->where('status', 'confirmed')
                ->orderBy('payment_date', 'desc')
                ->limit(4)
                ->get();

            return view('dashboard', compact(
                'totalStudents',
                'paidInvoices',
                'partiallyPaidInvoices',
                'pendingInvoices',
                'overdueInvoices',
                'totalUsers',
                'totalRevenue',
                'revenueChart',
                'paymentChartData',
                'recentPayments',
                'totalBilled',  
                'totalPaid',
                'paymentRate'
            ));

        } catch (\Exception $e) {
            // Fallback para quando as tabelas não existirem
            return view('dashboard', [
                'totalStudents' => 0,
                'paidInvoices' => 0,
                'partiallyPaidInvoices' => 0,
                'pendingInvoices' => 0,
                'overdueInvoices' => 0,
                'totalUsers' => 0,
                'totalRevenue' => 0,
                'revenueChart' => ['labels' => [], 'data' => []],
                'paymentChartData' => [0, 0, 0, 0],
                'recentPayments' => collect(),
            ]);
        }
    }
}