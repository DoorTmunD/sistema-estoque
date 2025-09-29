<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LogAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !in_array($user->nivel, ['super-admin', 'adm'])) {
                abort(403, 'Acesso restrito ao log de auditoria.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
{
    // Parâmetros de filtro
    $filtros = $request->validate([
        'user' => 'nullable|exists:users,id',
        'log_name' => 'nullable|string',
        'search' => 'nullable|string|max:255',
        'data_inicio' => 'nullable|date',
        'data_fim' => 'nullable|date',
        'per_page' => 'nullable|integer|min:5|max:100',
        'sort' => 'nullable|in:asc,desc', // ADICIONADO!
    ]);

    $sort = $filtros['sort'] ?? 'desc'; // desc é padrão, igual já fazia

    $query = Activity::with('causer')->orderBy('created_at', $sort);

    if (!empty($filtros['user'])) {
        $query->where('causer_id', $filtros['user']);
    }
    if (!empty($filtros['log_name'])) {
        $query->where('log_name', $filtros['log_name']);
    }
    if (!empty($filtros['search'])) {
        $query->where(function ($q) use ($filtros) {
            $q->where('description', 'like', '%' . $filtros['search'] . '%')
              ->orWhere('properties', 'like', '%' . $filtros['search'] . '%');
        });
    }
    if (!empty($filtros['data_inicio'])) {
        $query->where('created_at', '>=', $filtros['data_inicio']);
    }
    if (!empty($filtros['data_fim'])) {
        $query->where('created_at', '<=', $filtros['data_fim']);
    }

    $perPage = $filtros['per_page'] ?? 30;
    $logs = $query->paginate($perPage)->appends($request->all());

    // Prepara os dados para os selects/filtros
    $users = User::orderBy('name')->get();
    $logNames = Activity::select('log_name')->distinct()->pluck('log_name')->filter();

    return view('admin.logs.index', compact('logs', 'users', 'logNames'));
}

    // Exportação CSV dos logs filtrados (com limite seguro)
    public function exportCsv(Request $request)
    {
        $filtros = $request->validate([
            'user' => 'nullable|exists:users,id',
            'log_name' => 'nullable|string',
            'search' => 'nullable|string|max:255',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
        ]);

        $query = Activity::with('causer')->latest();

        if (!empty($filtros['user'])) {
            $query->where('causer_id', $filtros['user']);
        }
        if (!empty($filtros['log_name'])) {
            $query->where('log_name', $filtros['log_name']);
        }
        if (!empty($filtros['search'])) {
            $query->where(function ($q) use ($filtros) {
                $q->where('description', 'like', '%' . $filtros['search'] . '%')
                  ->orWhere('properties', 'like', '%' . $filtros['search'] . '%');
            });
        }
        if (!empty($filtros['data_inicio'])) {
            $query->where('created_at', '>=', $filtros['data_inicio']);
        }
        if (!empty($filtros['data_fim'])) {
            $query->where('created_at', '<=', $filtros['data_fim']);
        }

        // Limite de registros para evitar overload em CSV
        $logs = $query->limit(5000)->get();

        $filename = 'logs_auditoria_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Data', 'Usuário', 'Tipo', 'Descrição', 'Registro']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->created_at->format('d/m/Y H:i'),
                    $log->causer ? $log->causer->name : '-',
                    $log->log_name,
                    $log->description,
                    class_basename($log->subject_type)
                ]);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}