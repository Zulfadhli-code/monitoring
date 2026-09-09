<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        $auditLogs = AuditLog::with(['user', 'fasilitas'])
            ->latest()
            ->paginate(20);

        return view('audit-logs', compact('auditLogs'));
    }
}