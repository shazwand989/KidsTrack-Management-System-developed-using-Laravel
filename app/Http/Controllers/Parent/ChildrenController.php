<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChildrenController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $children = $this->getChildren($user);
        $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();

        $childIds = $children->pluck('id')->toArray();
        $attendances = Attendance::whereIn('child_id', $childIds)->whereDate('date', $today)->get()->keyBy('child_id');

        foreach ($children as $child) {
            $att = $attendances->get($child->id);
            if ($att) {
                if ($att->checkout_time || in_array($att->status, ['checkout', 'late_checkout'])) {
                    $child->status_today = 'Checked Out';
                    $child->status_color = 'bg-yellow-100 text-yellow-800';
                } elseif ($att->checkin_time || in_array($att->status, ['present', 'late'])) {
                    $child->status_today = 'Checked In';
                    $child->status_color = 'bg-green-100 text-green-800';
                } else {
                    $child->status_today = 'Pending';
                    $child->status_color = 'bg-gray-100 text-gray-800';
                }
            } else {
                $child->status_today = 'Pending';
                $child->status_color = 'bg-gray-100 text-gray-800';
            }
        }

        return view('parent.children.index', compact('children'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $child = $this->findChild($user, $id);
        if (!$child) return redirect()->route('parent.children.index')->with('error', 'Anak tidak ditemui.');

        $totalPresent = $child->attendances->whereIn('status', ['present', 'checkin'])->count();
        $totalAbsent = $child->attendances->where('status', 'absent')->count();
        $totalLate = $child->attendances->where('status', 'late')->count();

        return view('parent.children.show', compact('child', 'totalPresent', 'totalAbsent', 'totalLate'));
    }

    private function getChildren($user)
    {
        return $user->children()->with('classroom')->get();
    }

    private function findChild($user, $id)
    {
        return $user->children()->with(['classroom', 'attendances'])->find($id);
    }
}
