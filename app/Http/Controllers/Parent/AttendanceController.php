<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\SimulationClock;
use App\Services\AttendanceSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected AttendanceSummaryService $summaryService;

    public function __construct(AttendanceSummaryService $summaryService)
    {
        $this->summaryService = $summaryService;
    }
    private function getChildren(\App\Models\User $user): \Illuminate\Support\Collection
    {
        if (!$user) {
            return collect();
        }
        return $user->children()->with('classroom')->get() ?? collect();
    }

    public function index()
    {
        $user = Auth::user();
        $children = $this->getChildren($user);
        $attendance = Attendance::whereIn('child_id', $children->pluck('id'))
            ->with(['child', 'child.classroom'])
            ->whereDate('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();

        // Build summaries
        $summaries = [];
        foreach ($attendance as $att) {
            $summaries[$att->id] = $this->summaryService->getAttendanceSummary($att);
        }

        return view('parent.attendance.index', compact('children', 'attendance', 'summaries'));
    }

    public function calendar()
    {
        $children = $this->getChildren(Auth::user());
        return view('parent.attendance.calendar', compact('children'));
    }

    public function childAttendance(int $id)
    {
        $children = $this->getChildren(Auth::user());
        $child = Child::whereIn('id', $children->pluck('id'))->with('classroom')->findOrFail($id);

        /** @var \Illuminate\Pagination\LengthAwarePaginator $attendance */
        $attendance = Attendance::where('child_id', $child->id)
            ->with('child.classroom')
            ->orderBy('date', 'desc')
            ->paginate(20);

        // Build attendance summaries for each record
        $summaries = [];
        foreach ($attendance as $att) {
            $summaries[$att->id] = $this->summaryService->getAttendanceSummary($att);
        }

        return view('parent.attendance.child', compact('child', 'attendance', 'summaries'));
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

        $attendances = Attendance::with(['child.classroom'])
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

                // Build attendance summary for rich modal
                $summary = $this->summaryService->getAttendanceSummary($attendance);
                $classroom = $attendance->child->classroom ?? null;

                return [
                    'id' => $attendance->id,
                    'title' => $attendance->child->name ?? 'Child',
                    'start' => Carbon::parse($attendance->date)->format('Y-m-d'),
                    'allDay' => true,
                    'backgroundColor' => $color,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'child_name' => $attendance->child->name ?? 'Child',
                        'classroom' => $classroom->name ?? '-',
                        'status' => $status,
                        'checkin_time' => $attendance->checkin_time
                            ? Carbon::parse($attendance->checkin_time)->format('h:i A')
                            : null,
                        'checkin_status' => $summary['checkin']['status'] ?? 'unknown',
                        'checkin_label' => $summary['checkin']['status_label'] ?? '—',
                        'checkin_minutes' => $summary['checkin']['minutes_diff'] ?? 0,
                        'checkout_time' => $attendance->checkout_time
                            ? Carbon::parse($attendance->checkout_time)->format('h:i A')
                            : null,
                        'checkout_status' => $summary['checkout']['status'] ?? 'unknown',
                        'checkout_label' => $summary['checkout']['status_label'] ?? '—',
                        'checkout_minutes' => $summary['checkout']['minutes_diff'] ?? 0,
                        'schedule_in' => $summary['schedule']['morning_end'] ?? '07:30',
                        'schedule_out' => $summary['schedule']['class_end'] ?? $summary['schedule']['evening_end'] ?? '17:00',
                        'is_late' => in_array($attendance->status, ['late', 'late_checkout']),
                        'color' => $color,
                    ],
                ];
            });

        return response()->json($attendances->values());
    }
}
