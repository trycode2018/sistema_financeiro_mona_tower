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
{
    public function index()
    {
        try {
            $totalStudents = Student::count();
            $paidInvoices = Invoice::where('status', 'paid')->count();
            $pendingInvoices = Invoice::where('status', 'pending')->count();
            $overdueInvoices = Invoice::where('status', 'overdue')->count();
            $totalUsers = User::count();
            $totalRevenue = Payment::sum('amount');

            // Revenue data for the last 6 months
            $revenueData = Payment::select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total')
            )
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
                (int) $pendingInvoices,
                (int) $overdueInvoices,
            ];

            $recentPayments = Payment::with(['invoice.student'])
                ->orderBy('payment_date', 'desc')
                ->limit(5)
                ->get();

            return view('dashboard', compact(
                'totalStudents',
                'paidInvoices',
                'pendingInvoices',
                'overdueInvoices',
                'totalUsers',
                'totalRevenue',
                'revenueChart',
                'paymentChartData',
                'recentPayments'
            ));

        } catch (\Exception $e) {
            // Fallback para quando as tabelas não existirem
            return view('dashboard', [
                'totalStudents' => 0,
                'paidInvoices' => 0,
                'pendingInvoices' => 0,
                'overdueInvoices' => 0,
                'totalUsers' => 0,
                'totalRevenue' => 0,
                'revenueChart' => ['labels' => [], 'data' => []],
                'paymentChartData' => [0, 0, 0],
                'recentPayments' => collect(),
            ]);
        }
    }
}