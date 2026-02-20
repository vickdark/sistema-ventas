<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = ActivityLog::with('user');

            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                      ->orWhere('model_type', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            
            $logs = $query->orderBy('created_at', 'desc')
                          ->offset($offset)
                          ->limit($limit)
                          ->get();

            return response()->json([
                'data' => $logs->map(function($log) {
                    return [
                        'id' => $log->id,
                        'user' => $log->user ? $log->user->name : 'Sistema',
                        'action' => $this->formatAction($log->action),
                        'model' => class_basename($log->model_type),
                        'description' => $log->description,
                        'date' => $log->created_at->format('d/m/Y H:i:s'),
                    ];
                }),
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('activity-logs.index'),
                'show' => route('activity-logs.show', ':id'),
            ]
        ];
        
        return view('tenant.activity_logs.index', compact('config'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return view('tenant.activity_logs.show', compact('log'));
    }

    private function formatAction($action)
    {
        $badges = [
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'login' => 'primary',
            'logout' => 'secondary'
        ];

        $color = $badges[$action] ?? 'dark';
        return '<span class="badge bg-' . $color . '">' . strtoupper($action) . '</span>';
    }
}
