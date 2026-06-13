<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Child;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // ... (existing methods: index, create, store, calendar, checkin, checkout, getData, show, edit, update, destroy)

    /**
     * Search page - parent taip nama anak
     */
    public function search()
    {
        return view('attendance.search');
    }

    /**
     * AJAX - live search nama anak
     */
    public function searchResults(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $children = Child::with(['classroom', 'parent'])
            ->where('name', 'LIKE', "%{$q}%")
            ->where('is_active', true)
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id'        => $c->id,
                'name'      => $c->name,
                'classroom' => $c->classroom->name ?? 'No class',
                'age'       => $c->age,
                'photo'     => $c->photo ? asset('storage/' . $c->photo) : null,
                'initial'   => strtoupper(substr($c->name, 0, 1)),
            ]);

        return response()->json($children);
    }

    /**
     * Verify no phone parent/guardian - return JSON untuk AJAX
     */
    public function verifyPhone(Request $request, Child $child)
    {
        $request->validate(['phone' => 'required|string']);

        $input   = preg_replace('/\D/', '', $request->phone);
        $matched = false;

        if ($child->parent) {
            if (preg_replace('/\D/', '', $child->parent->phone ?? '') === $input) $matched = true;
        }
        if (!$matched && $child->secondParent) {
            if (preg_replace('/\D/', '', $child->secondParent->phone ?? '') === $input) $matched = true;
        }
        if (!$matched && $child->guardian) {
            if (preg_replace('/\D/', '', $child->guardian->phone ?? '') === $input) $matched = true;
        }

        if (!$matched) {
            return response()->json([
                'success' => false,
                'message' => 'No phone tidak sepadan. Cuba semula.'
            ]);
        }

        session(["verified_{$child->id}" => true]);

        return response()->json([
            'success' => true,
            'message' => 'Identiti disahkan!'
        ]);
    }

    /**
     * Landing page after scanning QR code
     */
    public function landing()
    {
        $children = Child::with('classroom')->where('is_active', true)->get();
        return view('attendance.landing', compact('children'));
    }

    /**
     * Display child profile for check-in/check-out
     */
    public function childProfile(Child $child)
    {
        $verified = session("verified_{$child->id}", false);

        // Kalau belum verify, redirect balik ke search
        if (!$verified) {
            return redirect()->route('attendance.search')
                ->with('error', 'Sila verify no phone dahulu.');
        }

        $attendance   = Attendance::where('child_id', $child->id)
            ->whereDate('date', today())
            ->first();

        $status       = $attendance ? $attendance->status : 'absent';
        $checkinTime  = $attendance ? $attendance->checkin_time : null;
        $checkoutTime = $attendance ? $attendance->checkout_time : null;
        $dropOffBy    = $attendance ? $attendance->drop_off_by : null;
        $pickupBy     = $attendance ? $attendance->pickup_by : null;

        return view('attendance.child-profile', compact(
            'child', 'verified', 'status', 'checkinTime', 'checkoutTime', 'dropOffBy', 'pickupBy'
        ));
    }

    /**
     * Process check-in for a child
     */
    public function processCheckin(Request $request, Child $child)
    {
        if (!session("verified_{$child->id}")) {
            return redirect()->route('attendance.search')
                ->with('error', 'Sila verify no phone dahulu.');
        }

        $today       = now()->toDateString();
        $currentTime = now();

        if ($currentTime->hour < 7) {
            return redirect()->back()->with('error', 'Check-in only allowed after 7:00 AM');
        }

        if ($currentTime->hour >= 12 && $currentTime->hour < 14) {
            return redirect()->back()->with('error', 'Morning check-in closed. Please contact admin.');
        }

        $existing = Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->checkin_time) {
            return redirect()->back()->with('error', 'Already checked in today!');
        }

        if ($existing) {
            $existing->update([
                'status'       => 'checkin',
                'checkin_time' => $currentTime,
                'drop_off_by'  => $request->drop_off_by ?? null,
            ]);
        } else {
            Attendance::create([
                'child_id'     => $child->id,
                'date'         => $today,
                'status'       => 'checkin',
                'checkin_time' => $currentTime,
                'drop_off_by'  => $request->drop_off_by ?? null,
            ]);
        }

        session()->forget("verified_{$child->id}");

        return redirect()->back()->with('success', "✅ {$child->name} checked in at " . $currentTime->format('h:i A'));
    }

    /**
     * Process check-out for a child
     */
    public function processCheckout(Request $request, Child $child)
    {
        if (!session("verified_{$child->id}")) {
            return redirect()->route('attendance.search')
                ->with('error', 'Sila verify no phone dahulu.');
        }

        $today       = now()->toDateString();
        $currentTime = now();

        $attendance = Attendance::where('child_id', $child->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->checkin_time) {
            return redirect()->back()->with('error', 'Must check-in first before check-out!');
        }

        if ($attendance->checkout_time) {
            return redirect()->back()->with('error', 'Already checked out today!');
        }

        if ($currentTime->hour < 12) {
            return redirect()->back()->with('error', 'Check-out only allowed after 12:00 PM');
        }

        if ($currentTime->hour >= 20) {
            return redirect()->back()->with('error', 'Check-out closed after 8:00 PM. Please contact admin.');
        }

        $attendance->update([
            'status'        => 'checkout',
            'checkout_time' => $currentTime,
            'pickup_by'     => $request->pickup_by ?? null,
        ]);

        session()->forget("verified_{$child->id}");

        return redirect()->back()->with('success', "📤 {$child->name} checked out at " . $currentTime->format('h:i A'));
    }

    /**
     * Get current status for a child (API)
     */
    public function getStatus(Child $child)
    {
        $attendance = Attendance::where('child_id', $child->id)
            ->whereDate('date', today())
            ->first();

        return response()->json([
            'child_id'      => $child->id,
            'name'          => $child->name,
            'status'        => $attendance ? $attendance->status : 'absent',
            'checkin_time'  => $attendance ? $attendance->checkin_time : null,
            'checkout_time' => $attendance ? $attendance->checkout_time : null,
        ]);
    }

    /**
     * Get all children status for today (API)
     */
    public function getAllStatus()
    {
        $children = Child::with('classroom')->where('is_active', true)->get();

        $today       = today()->toDateString();
        $attendances = Attendance::whereDate('date', $today)
            ->get()
            ->keyBy('child_id');

        $results = [];
        foreach ($children as $child) {
            $att       = $attendances->get($child->id);
            $results[] = [
                'id'            => $child->id,
                'name'          => $child->name,
                'classroom'     => $child->classroom->name ?? null,
                'status'        => $att ? $att->status : 'absent',
                'checkin_time'  => $att ? $att->checkin_time : null,
                'checkout_time' => $att ? $att->checkout_time : null,
            ];
        }

        return response()->json([
            'date'     => $today,
            'children' => $results,
        ]);
    }
}