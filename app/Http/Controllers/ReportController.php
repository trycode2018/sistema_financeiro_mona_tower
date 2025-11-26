<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function financial()
    {
        $monthlyRevenue = Payment::select(
            DB::raw('YEAR(payment_date) as year'),
            DB::raw('MONTH(payment_date) as month'),
            DB::raw('SUM(amount) as total')
        )
        ->whereYear('payment_date', date('Y'))
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        $invoiceStatus = Invoice::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $topStudents = Invoice::with('student')
            ->select('student_id', DB::raw('SUM(total_amount) as total_billed'))
            ->groupBy('student_id')
            ->orderBy('total_billed', 'desc')
            ->limit(10)
            ->get();

        return view('reports.financial', compact('monthlyRevenue', 'invoiceStatus', 'topStudents'));
    }
}