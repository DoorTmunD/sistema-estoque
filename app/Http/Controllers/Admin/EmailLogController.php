<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = EmailLog::latest()->paginate(30);

        return view('admin.logs.email', compact('logs'));
    }

    public function show($id)
    {
        $log = EmailLog::findOrFail($id);
        return view('admin.logs.email_show', compact('log'));
    }
}