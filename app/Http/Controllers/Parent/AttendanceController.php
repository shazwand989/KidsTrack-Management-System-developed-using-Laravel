<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\ParentModel;
use App\Models\SecondParent;
use App\Models\Guardian;
use App\Models\SimulationClock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private function getChildren($user)
    {
        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('id', Auth::id())->first();
            return $parent ? $parent->children : collect();
        }
        if ($user->role === 'parent2') {
            $sp = SecondParent::where('id', Auth::id())->first();
            if ($sp && ($mp = ParentModel::find($sp->parent_id))) return $mp->children;
        }
        if ($user->role === 'guardian') {
            $g = Guardian::where('id', Auth::id())->first();
            return $g ? $g->children : collect();
        }
        return collect();
    }

    public function index()
    {
        $children = $this->getChildren(Auth::user());
        $attendance = Attendance::whereIn('child_id', $children->pluck('id'))
            ->with(['child', 'child.classroom'])
            ->whereDate('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();
        return view('parent.attendance.index', compact('children', 'attendance'));
    }

    public function calendar()
    {
        $children = $this->getChildren(Auth::user());
        return view('parent.attendance.calendar', compact('children'));
    }

    public function childAttendance($id)
    {
        $children = $this->getChildren(Auth::user());
        $child = Child::whereIn('id', $children->pluck('id'))->findOrFail($id);
        $attendance = Attendance::where('child_id', $child->id)->orderBy('date', 'desc')->paginate(20);
        return view('parent.attendance.child', compact('child', 'attendance'));
    }

    public function calendarData(Request $request)
    {
        $children = $this->getChildren(Auth::user());
        $childIds = $children->pluck('id')->toArray();

        // FullCalendar sends 'start' and 'end' as ISO 8601 strings
        $start = $request->input('start');
        $end = $request->input('end');

        if ($start && $end) {
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
        } else {
            // Fallback to month/year params
            $month = $request->input('month', Carbon::now()->month);
            $year = $request->input('year', Carbon::now()->year);
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        }

        $attendances = Attendance::with(['child'])
            ->whereIn('child_id', $childIds)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->map(function ($attendance) {
                $status = $attendance->status;
                if ($attendance->checkout_time && $attendance->checkin_time) {
                    $status = 'checkout';
                } elseif ($attendance->checkin_time && !$attendance->checkout_time) {
                    $status = $attendance->status ?? 'present';
                }

                $color = '#43a047'; // green - present/checkin
                if (in_array($status, ['late', 'late_checkout'])) {
                    $color = '#e53935'; // red
                } elseif ($status === 'checkout') {
                    $color = '#1e88e5'; // blue
                } elseif ($status === 'absent') {
                    $color = '#fb8c00'; // orange
                }

                return [
                    'id' => $attendance->id,
                    'title' => $attendance->child->name ?? 'Child',
                    'start' => Carbon::parse($attendance->date)->format('Y-m-d'),
                    'allDay' => true,
                    'backgroundColor' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'child_name' => $attendance->child->name ?? 'Child',
                        'status' => $status,
                        'checkin_time' => $attendance->checkin_time 
                            ? Carbon::parse($attendance->checkin_time)->format('h:i A') 
                            : null,
                        'checkout_time' => $attendance->checkout_time 
                            ? Carbon::parse($attendance->checkout_time)->format('h:i A') 
                            : null,
                        'is_late' => in_array($attendance->status, ['late', 'late_checkout']),
                        'color' => $color,
                    ],
                ];
            });

        return response()->json($attendances->values());
    }
}
