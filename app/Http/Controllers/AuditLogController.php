<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        // Recupera os logs com os dados do utilizador associado, ordenados pelo mais recente
        $logs = AuditLog::with('user')->latest()->paginate(6);

        return view('admin.audit_logs.index', compact('logs'));
    }
    public function show(AuditLog $auditLog)
    {
        // Passamos o log específico para a view de detalhes
        return view('admin.audit_logs.show', compact('auditLog'));
    }
}
