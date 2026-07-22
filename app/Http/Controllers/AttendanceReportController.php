<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Classroom;
use App\Services\AttendanceReportService;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function __construct(
        protected AttendanceReportService $reportService
    ) {}

    /**
     * Show the report index / class report.
     */
    public function classReport(Request $request)
    {
        $filters = $this->parseFilters($request);
        $data = $this->reportService->getClassReport($filters);
        $classrooms = Classroom::orderBy('name')->get();
        $students = Child::orderBy('name')->get();

        return view('attendance.reports.class', array_merge($data, [
            'filters'    => $filters,
            'classrooms' => $classrooms,
            'students'   => $students,
        ]));
    }

    /**
     * Show individual student report.
     */
    public function studentReport(Request $request, Child $child)
    {
        $filters = $this->parseFilters($request);
        $data = $this->reportService->getStudentReport($child->id, $filters);
        $classrooms = Classroom::orderBy('name')->get();
        $students = Child::orderBy('name')->get();

        return view('attendance.reports.student', array_merge($data, [
            'filters'    => $filters,
            'classrooms' => $classrooms,
            'students'   => $students,
        ]));
    }

    /**
     * Export class report.
     */
    public function exportClass(Request $request)
    {
        $filters = $this->parseFilters($request);
        $filters['per_page'] = 9999; // all records
        $data = $this->reportService->getClassReport($filters);

        return $this->exportResponse($data['rows'], 'class-attendance-report', $request->format ?? 'csv');
    }

    /**
     * Export student report.
     */
    public function exportStudent(Request $request, Child $child)
    {
        $filters = $this->parseFilters($request);
        $data = $this->reportService->getStudentReport($child->id, $filters);

        return $this->exportResponse($data['rows'], 'student-attendance-' . $child->id, $request->format ?? 'csv');
    }

    /**
     * Parse filter inputs.
     */
    private function parseFilters(Request $request): array
    {
        return [
            'month'     => $request->input('month', now()->month),
            'year'      => $request->input('year', now()->year),
            'class'     => $request->input('class'),
            'student'   => $request->input('student'),
            'status'    => $request->input('status'),
            'search'    => $request->input('search'),
            'date_from' => $request->input('date_from'),
            'date_to'   => $request->input('date_to'),
            'per_page'  => $request->input('per_page', 15),
        ];
    }

    /**
     * Generate CSV export response.
     */
    private function exportResponse(array $rows, string $filename, string $format)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF"); // BOM for Excel

            if (!empty($rows)) {
                fputcsv($out, array_keys($rows[0]));
                foreach ($rows as $row) {
                    fputcsv($out, $row);
                }
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
