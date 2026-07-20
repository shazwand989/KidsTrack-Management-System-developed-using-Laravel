<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\ParentModel;
use App\Models\SecondParent;
use App\Models\Guardian;
use App\Models\SimulationClock;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private function getChildren($user)
    {
        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
            return $parent ? $parent->children : collect();
        }
        if ($user->role === 'parent2') {
            $sp = SecondParent::where('user_id', Auth::id())->first();
            if ($sp && ($mp = ParentModel::find($sp->parent_id))) return $mp->children;
        }
        if ($user->role === 'guardian') {
            $g = Guardian::where('user_id', Auth::id())->first();
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
}
