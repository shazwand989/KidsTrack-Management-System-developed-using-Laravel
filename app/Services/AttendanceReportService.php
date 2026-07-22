<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Child;
use App\Models\Classroom;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceReportService
{
    public function __construct(
        protected AttendanceSummaryService $summaryService
    ) {}

    /**
     * Get class report data with aggregates.
     */
    public function getClassReport(array $filters): array
    {
        $query = Child::with(['classroom', 'attendances' => function ($q) use ($filters) {
            $this->applyDateFilters($q, $filters);
        }]);

        if (!empty($filters['class'])) {
            $query->where('classroom_id', $filters['class']);
        }

        if (!empty($filters['student'])) {
            $query->where('id', $filters['student']);
        }

        if (!empty($filters['search'])) {
            $q = $filters['search'];
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('ic_number', 'like', "%{$q}%");
            });
        }

        $perPage = $filters['per_page'] ?? 15;
        $children = $query->paginate($perPage);

        // Compute stats for each child
        $rows = [];
        $totalDays = $this->countSchoolDays($filters['month'] ?? now()->month, $filters['year'] ?? now()->year);

        foreach ($children as $child) {
            $stats = $this->computeChildStats($child, $filters);
            $present = $stats['present'];
            $absent = max(0, $totalDays - $present);
            $pct = $totalDays > 0 ? round(($present / $totalDays) * 100, 1) : 0;

            $rows[] = [
                'child_id'   => $child->id,
                'name'       => $child->name,
                'classroom'  => $child->classroom->name ?? '-',
                'total_days' => $totalDays,
                'present'    => $present,
                'absent'     => $absent,
                'late'       => $stats['late'],
                'early'      => $stats['early'],
                'percentage' => $pct,
            ];
        }

        // Summary cards
        $summary = [
            'total_students' => $children->total(),
            'total_present'  => array_sum(array_column($rows, 'present')),
            'total_absent'   => array_sum(array_column($rows, 'absent')),
            'total_late'     => array_sum(array_column($rows, 'late')),
            'total_early'    => array_sum(array_column($rows, 'early')),
            'overall_pct'    => count($rows) > 0
                ? round(array_sum(array_column($rows, 'percentage')) / count($rows), 1)
                : 0,
        ];

        return [
            'rows'    => $rows,
            'summary' => $summary,
            'paginator' => $children,
        ];
    }

    /**
     * Get individual student detailed report.
     */
    public function getStudentReport(int $childId, array $filters): array
    {
        $child = Child::with('classroom')->findOrFail($childId);

        $query = Attendance::where('child_id', $childId)
            ->with('child.classroom')
            ->orderBy('date', 'desc');

        $this->applyDateFilters($query, $filters);

        $records = $query->get();

        // Build detailed rows with status
        $rows = [];
        $stats = ['present' => 0, 'late' => 0, 'early' => 0];

        foreach ($records as $att) {
            $summary = $this->summaryService->getAttendanceSummary($att);
            $isPresent = $att->checkin_time ? true : false;
            if ($isPresent) $stats['present']++;
            if ($summary['checkin']['status'] === 'late') $stats['late']++;
            if ($summary['checkout']['status'] === 'early') $stats['early']++;

            $rows[] = [
                'date'               => Carbon::parse($att->date)->format('d M Y'),
                'checkin_time'       => $att->checkin_time ? Carbon::parse($att->checkin_time)->format('h:i A') : '—',
                'checkout_time'      => $att->checkout_time ? Carbon::parse($att->checkout_time)->format('h:i A') : '—',
                'status_raw'         => $att->status ?? '—',
                'ci_status'          => $summary['checkin']['status_label'] ?? '—',
                'ci_status_class'    => $summary['checkin']['status_class'] ?? '',
                'co_status'          => $summary['checkout']['status_label'] ?? '—',
                'co_status_class'    => $summary['checkout']['status_class'] ?? '',
                'ci_mins'            => $summary['checkin']['minutes_diff'] ?? 0,
                'co_mins'            => $summary['checkout']['minutes_diff'] ?? 0,
                'schedule_in'        => $summary['schedule']['class_start'] ?? $summary['schedule']['morning_end'] ?? '—',
                'schedule_out'       => $summary['schedule']['class_end'] ?? $summary['schedule']['evening_end'] ?? '—',
                'note'               => $att->status_note ?? $att->late_reason ?? '',
            ];
        }

        $totalDays = $this->countSchoolDays($filters['month'] ?? null, $filters['year'] ?? null, $filters);
        $absent = max(0, $totalDays - $stats['present']);
        $pct = $totalDays > 0 ? round(($stats['present'] / $totalDays) * 100, 1) : 0;

        return [
            'child'    => $child,
            'rows'     => $rows,
            'summary'  => [
                'total_days' => $totalDays,
                'present'    => $stats['present'],
                'absent'     => $absent,
                'late'       => $stats['late'],
                'early'      => $stats['early'],
                'percentage' => $pct,
            ],
        ];
    }

    /**
     * Count school days in a given month/year, or within a date range.
     */
    private function countSchoolDays(?int $month, ?int $year, array $filters = []): int
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $start = Carbon::parse($filters['date_from']);
            $end = Carbon::parse($filters['date_to']);
        } else {
            $start = Carbon::create($year, $month, 1);
            $end = $start->copy()->endOfMonth();
        }

        // Cap at today
        $today = Carbon::now('Asia/Kuala_Lumpur')->startOfDay();
        if ($end->gt($today)) $end = $today;

        $days = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if (!$current->isWeekend()) $days++;
            $current->addDay();
        }
        return max(1, $days);
    }

    /**
     * Apply month/year or date range filters to an attendance query.
     */
    private function applyDateFilters($query, array $filters): void
    {
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('date', [$filters['date_from'], $filters['date_to']]);
        } elseif (!empty($filters['month']) && !empty($filters['year'])) {
            $query->whereMonth('date', $filters['month'])
                  ->whereYear('date', $filters['year']);
        }

        if (!empty($filters['status'])) {
            $statuses = (array) $filters['status'];
            $query->where(function ($q) use ($statuses) {
                foreach ($statuses as $s) {
                    if ($s === 'present') {
                        $q->orWhereIn('status', ['present', 'checkin', 'checkout']);
                    } elseif ($s === 'late') {
                        $q->orWhereIn('status', ['late', 'late_checkout']);
                    } elseif ($s === 'early') {
                        $q->orWhereNotNull('checkout_time')
                          ->where('checkout_time', '<', '17:00:00');
                    } elseif ($s === 'absent') {
                        $q->orWhere('status', 'absent');
                    }
                }
            });
        }
    }

    /**
     * Compute aggregated stats for a single child from their loaded attendances.
     */
    private function computeChildStats(Child $child, array $filters): array
    {
        $stats = ['present' => 0, 'late' => 0, 'early' => 0];

        foreach ($child->attendances as $att) {
            if ($att->checkin_time) {
                $stats['present']++;
                $summary = $this->summaryService->getAttendanceSummary($att);
                if ($summary['checkin']['status'] === 'late') $stats['late']++;
                if ($summary['checkout']['status'] === 'early') $stats['early']++;
            }
        }

        return $stats;
    }
}
