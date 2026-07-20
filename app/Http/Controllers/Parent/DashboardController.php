<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\ParentModel;
use App\Models\SecondParent;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = null;
        $secondParent = null;
        $guardian = null;
        $children = collect();

        if (in_array($user->role, ['parent', 'parent1'])) {
            $parent = ParentModel::where('user_id', Auth::id())->first();
            if ($parent) {
                $children = $parent->children;
            } else {
                return redirect()->route('profile.edit')->with('error', 'Sila lengkapkan profil anda terlebih dahulu.');
            }
        }
        
        if ($user->role === 'parent2') {
            $secondParent = SecondParent::where('user_id', Auth::id())->first();
            if ($secondParent) {
                $mainParent = ParentModel::find($secondParent->parent_id);
                if ($mainParent) $children = $mainParent->children;
            } else {
                return redirect()->route('profile.edit')->with('error', 'Sila lengkapkan profil second parent anda.');
            }
        }
        
        if ($user->role === 'guardian') {
            $guardian = Guardian::where('user_id', Auth::id())->first();
            if ($guardian) {
                $children = $guardian->children;
            } else {
                return redirect()->route('profile.edit')->with('error', 'Sila lengkapkan profil guardian anda.');
            }
        }

        if (!$parent && !$secondParent && !$guardian) {
            return redirect()->route('dashboard')->with('error', 'Akses tidak dibenarkan.');
        }

        $totalChildren = $children->count();
        $today = Carbon::now('Asia/Kuala_Lumpur')->toDateString();
        $childIds = $children->pluck('id')->toArray();
        $attendances = Attendance::whereIn('child_id', $childIds)->whereDate('date', $today)->get()->keyBy('child_id');
        
        $attendanceToday = $attendances->filter(function($att) {
            return $att->checkin_time && !$att->checkout_time;
        })->count();
        
        foreach ($children as $child) {
            $att = $attendances->get($child->id);
            if ($att) {
                if ($att->checkout_time || in_array($att->status, ['checkout', 'late_checkout'])) {
                    $child->status_today = 'Checked Out';
                    $child->status_class = 'checkout';
                } elseif ($att->checkin_time || in_array($att->status, ['present', 'late'])) {
                    $child->status_today = 'Checked In';
                    $child->status_class = 'checkin';
                } else {
                    $child->status_today = 'Pending';
                    $child->status_class = 'pending';
                }
            } else {
                $child->status_today = 'Pending';
                $child->status_class = 'pending';
            }
        }

        return view('parent.dashboard', compact(
            'parent', 'secondParent', 'guardian', 'children',
            'totalChildren', 'attendanceToday'
        ));
    }

    public function notifications()
    {
        return view('parent.notifications', ['user' => Auth::user()]);
    }

    public function payment()
    {
        return view('parent.payment', ['user' => Auth::user()]);
    }

    public function fine()
    {
        return view('parent.fine', ['user' => Auth::user()]);
    }
}
