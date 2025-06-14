<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'inquiry'])
            ->orderByDesc('timestamp');

        // Optional search filters
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('inquiry_id')) {
            $query->where('inquiry_id', $request->inquiry_id);
        }

        $logs = $query->paginate(20);

        return view('admin.audit_logs.index', compact('logs'));
    }

    /**
     * Show a specific audit log entry (optional).
     */
    public function show($id)
    {
        $log = AuditLog::with(['user', 'inquiry'])->findOrFail($id);
        return view('admin.audit_logs.show', compact('log'));
    }
}
