<?php

namespace App\Http\Controllers;

use App\Models\StudentTimetable;
use App\Models\User;
use App\Models\Subject;
use App\Models\Hall;
use App\Models\LecturerGroup;
use App\Models\Day;
use Illuminate\Http\Request;

class StudentTimetableController extends Controller
{
    public function index()
    {
        $studentTimetables = StudentTimetable::with([
            'user',
            'subject',
            'day',
            'hall',
            'lecturerGroup'
        ])->latest()->get();

        return view('student_timetables.index', compact('studentTimetables'));
    }

    public function create()
    {
        // Only show students, not admin
        $users = User::where('role', 'student')->get();

        $subjects = Subject::all();
        $halls = Hall::all();
        $lecturerGroups = LecturerGroup::all();
        $days = Day::all();

        return view('student_timetables.create', compact(
            'users',
            'subjects',
            'halls',
            'lecturerGroups',
            'days'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],

            'day_id' => [
                'required',
                'exists:days,id',
            ],

            'hall_id' => [
                'required',
                'exists:halls,id',
            ],

            'lecturer_group_id' => [
                'nullable',
                'exists:lecturer_groups,id',
            ],

            'time_from' => [
                'required',
                'date_format:H:i',
            ],

            'time_to' => [
                'required',
                'date_format:H:i',
                'after:time_from',
            ],
        ], [
            'user_id.required' => 'Please select a student.',
            'user_id.exists' => 'Selected student does not exist.',

            'subject_id.required' => 'Please select a subject.',
            'subject_id.exists' => 'Selected subject does not exist.',

            'day_id.required' => 'Please select a day.',
            'day_id.exists' => 'Selected day does not exist.',

            'hall_id.required' => 'Please select a hall.',
            'hall_id.exists' => 'Selected hall does not exist.',

            'lecturer_group_id.exists' => 'Selected lecturer group does not exist.',

            'time_from.required' => 'Start time is required.',
            'time_from.date_format' => 'Start time must be in correct format.',

            'time_to.required' => 'End time is required.',
            'time_to.date_format' => 'End time must be in correct format.',
            'time_to.after' => 'End time must be after start time.',
        ]);

        $timeFrom = date('H:i:s', strtotime($request->time_from));
        $timeTo = date('H:i:s', strtotime($request->time_to));

        // Prevent timetable clash for the same student on the same day
        $clash = StudentTimetable::where('id', $request->user_id)
            ->where('day_id', $request->day_id)
            ->where(function ($query) use ($timeFrom, $timeTo) {
                $query->where('time_from', '<', $timeTo)
                    ->where('time_to', '>', $timeFrom);
            })
            ->exists();

        if ($clash) {
            return back()
                ->with('error', 'Sorry, this student already has a class on the same day and time. Please choose another time.')
                ->withInput();
        }

        StudentTimetable::create([
            'user_id' => $validated['user_id'],
            'subject_id' => $validated['subject_id'],
            'day_id' => $validated['day_id'],
            'hall_id' => $validated['hall_id'],
            'lecturer_group_id' => $validated['lecturer_group_id'] ?? null,
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
        ]);

        return redirect()->route('student-timetables.index')
            ->with('success', 'Student timetable created successfully!');
    }

    public function show(StudentTimetable $student_timetable)
    {
        $student_timetable->load([
            'user',
            'subject',
            'day',
            'hall',
            'lecturerGroup'
        ]);

        return view('student_timetables.show', compact('student_timetable'));
    }

    public function edit(StudentTimetable $student_timetable)
    {
        // Only show students, not admin
        $users = User::where('role', 'student')->get();

        $subjects = Subject::all();
        $halls = Hall::all();
        $lecturerGroups = LecturerGroup::all();
        $days = Day::all();

        $student_timetable->load([
            'user',
            'subject',
            'day',
            'hall',
            'lecturerGroup'
        ]);

        return view('student_timetables.edit', compact(
            'student_timetable',
            'users',
            'subjects',
            'halls',
            'lecturerGroups',
            'days'
        ));
    }

    public function update(Request $request, StudentTimetable $student_timetable)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],

            'day_id' => [
                'required',
                'exists:days,id',
            ],

            'hall_id' => [
                'required',
                'exists:halls,id',
            ],

            'lecturer_group_id' => [
                'nullable',
                'exists:lecturer_groups,id',
            ],

            'time_from' => [
                'required',
                'date_format:H:i',
            ],

            'time_to' => [
                'required',
                'date_format:H:i',
                'after:time_from',
            ],
        ], [
            'user_id.required' => 'Please select a student.',
            'user_id.exists' => 'Selected student does not exist.',

            'subject_id.required' => 'Please select a subject.',
            'subject_id.exists' => 'Selected subject does not exist.',

            'day_id.required' => 'Please select a day.',
            'day_id.exists' => 'Selected day does not exist.',

            'hall_id.required' => 'Please select a hall.',
            'hall_id.exists' => 'Selected hall does not exist.',

            'lecturer_group_id.exists' => 'Selected lecturer group does not exist.',

            'time_from.required' => 'Start time is required.',
            'time_from.date_format' => 'Start time must be in correct format.',

            'time_to.required' => 'End time is required.',
            'time_to.date_format' => 'End time must be in correct format.',
            'time_to.after' => 'End time must be after start time.',
        ]);

        $timeFrom = date('H:i:s', strtotime($request->time_from));
        $timeTo = date('H:i:s', strtotime($request->time_to));

        // Prevent timetable clash, exclude current record
        $clash = StudentTimetable::where('id', $request->user_id)
            ->where('day_id', $request->day_id)
            ->where('id', '!=', $student_timetable->id)
            ->where(function ($query) use ($timeFrom, $timeTo) {
                $query->where('time_from', '<', $timeTo)
                    ->where('time_to', '>', $timeFrom);
            })
            ->exists();

        if ($clash) {
            return back()
                ->with('error', 'Sorry, this student already has a class on the same day and time. Please choose another time.')
                ->withInput();
        }

        $student_timetable->update([
            'user_id' => $validated['user_id'],
            'subject_id' => $validated['subject_id'],
            'day_id' => $validated['day_id'],
            'hall_id' => $validated['hall_id'],
            'lecturer_group_id' => $validated['lecturer_group_id'] ?? null,
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
        ]);

        return redirect()->route('student-timetables.index')
            ->with('success', 'Student timetable updated successfully!');
    }

    public function destroy(StudentTimetable $student_timetable)
    {
        $student_timetable->delete();

        return redirect()->route('student-timetables.index')
            ->with('success', 'Student timetable deleted successfully!');
    }
}