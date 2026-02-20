<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $query = Attendance::with('user');

            // If user is not admin, only show their own attendances
            if (!$user->hasRole('admin')) { 
                $query->where('user_id', $user->id);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            $attendances = $query->orderBy('clock_in', 'desc')->paginate(10);

            return response()->json($attendances);
        }

        return view('tenant.attendance.index');
    }

    public function status(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Find if user has an active shift (clock_in without clock_out)
        $activeShift = Attendance::where('user_id', $user->id)
            ->whereNull('clock_out')
            ->latest('clock_in')
            ->first();

        // Find if user has clocked in today
        $todayShift = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->latest('clock_in')
            ->first();

        return response()->json([
            'is_clocked_in' => (bool)$activeShift,
            'active_shift' => $activeShift,
            'today_shift' => $todayShift,
            'server_time' => Carbon::now()->format('H:i:s'),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user already has an active shift
        $activeShift = Attendance::where('user_id', $user->id)
            ->whereNull('clock_out')
            ->first();

        if ($activeShift) {
            return response()->json(['message' => 'Ya tienes un turno activo.'], 422);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'branch_id' => session('active_branch_id'), // Assuming branch is set in session
            'clock_in' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'present', // Default status, logic can be added for 'late'
            'ip_address' => $request->ip(),
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Entrada marcada exitosamente.',
            'attendance' => $attendance,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Check if it's clock-out needed
        if ($request->has('clock_out')) {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereNull('clock_out')
                ->findOrFail($id);

            $attendance->update([
                'clock_out' => Carbon::now(),
                'notes' => $attendance->notes . ($request->notes ? "\n" . $request->notes : ''),
            ]);

            return response()->json([
                'message' => 'Salida marcada exitosamente.',
                'attendance' => $attendance,
            ]);
        }
        
        return response()->json(['message' => 'Acción no válida.'], 400);
    }
}
