<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\Attendance;
use App\Models\ParentModel;
use App\Services\TelegramService;

class QRScanController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Show kiosk page
     */
    public function kiosk()
    {
        $children = Child::with('classroom')->get();
        return view('kiosk.index', compact('children'));
    }

    /**
     * Handle kiosk scan - NO LOGIN REQUIRED
     */
    public function handleKioskScan(Request $request)
    {
        try {
            $qrData = $request->input('qr_data');
            $parentId = $request->input('parent_id');
            
            // If QR data provided, find parent from QR
            if ($qrData) {
                $child = Child::where('qr_code', $qrData)->first();
                if ($child) {
                    $parentId = $child->parent_id;
                }
            }
            
            // If no parent_id, get first parent
            if (!$parentId) {
                $firstParent = ParentModel::first();
                if ($firstParent) {
                    $parentId = $firstParent->id;
                }
            }
            
            if (!$parentId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tiada parent dijumpai.',
                    'type' => 'NO_PARENT'
                ]);
            }

            $children = Child::where('parent_id', $parentId)->get();
            
            if ($children->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tiada rekod anak berdaftar.',
                    'type' => 'NO_CHILD'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Sila pilih anak untuk kehadiran',
                'type' => 'MORNING_IN',
                'children' => $children->map(function($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'classroom' => $child->classroom->name ?? 'No class',
                        'checked_in' => $this->isChildCheckedInToday($child->id),
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage(),
                'type' => 'SERVER_ERROR'
            ], 500);
        }
    }

    /**
     * Confirm attendance (check-in/out)
     */
    public function confirmAttendance(Request $request)
    {
        try {
            $request->validate([
                'child_ids' => 'required|array',
                'action' => 'required|in:checkin,checkout',
                'parent_id' => 'required|integer'
            ]);

            $parentId = $request->input('parent_id');
            $action = $request->input('action');
            $childIds = $request->input('child_ids');

            foreach ($childIds as $childId) {
                if ($action === 'checkin') {
                    $this->processCheckin($childId, $parentId);
                } else {
                    $this->processCheckout($childId, $parentId);
                }
            }

            // Send Telegram notification to parent
            $parent = ParentModel::find($parentId);
            if ($parent && $parent->telegram_notification && $parent->telegram_id) {
                $childNames = Child::whereIn('id', $childIds)->pluck('name')->join(', ');
                $message = $action === 'checkin' 
                    ? "🧸 KidsTrack Check-in Alert\n\n👶 Children: {$childNames}\n✅ Checked-in at: " . now()->format('h:i A') . "\n📅 Date: " . now()->format('d M Y')
                    : "🧸 KidsTrack Check-out Alert\n\n👶 Children: {$childNames}\n✅ Checked-out at: " . now()->format('h:i A') . "\n📅 Date: " . now()->format('d M Y');
                
                $this->telegram->sendMessage($parent->telegram_id, $message);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Attendance updated successfully for ' . count($childIds) . ' children!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if child is checked in today
     */
    private function isChildCheckedInToday($childId)
    {
        return Attendance::where('child_id', $childId)
            ->whereDate('date', today())
            ->whereNotNull('checkin_time')
            ->exists();
    }

    /**
     * Process check-in
     */
    private function processCheckin($childId, $parentId)
    {
        $attendance = Attendance::where('child_id', $childId)
            ->whereDate('date', today())
            ->first();
        
        if ($attendance) {
            $attendance->update([
                'status' => 'checkin',
                'checkin_time' => now()->format('H:i:s'),
                'drop_off_by' => 'Parent ID: ' . $parentId,
            ]);
        } else {
            Attendance::create([
                'child_id' => $childId,
                'parent_id' => $parentId,
                'date' => today(),
                'status' => 'checkin',
                'checkin_time' => now()->format('H:i:s'),
                'drop_off_by' => 'Parent ID: ' . $parentId,
            ]);
        }
    }

    /**
     * Process check-out
     */
    private function processCheckout($childId, $parentId)
    {
        $attendance = Attendance::where('child_id', $childId)
            ->whereDate('date', today())
            ->first();
        
        if ($attendance) {
            $attendance->update([
                'status' => 'checkout',
                'checkout_time' => now()->format('H:i:s'),
                'pickup_by' => 'Parent ID: ' . $parentId,
            ]);
        }
    }

    /**
     * Get attendance for a child
     */
    public function getAttendance($childId)
    {
        $attendance = Attendance::where('child_id', $childId)
            ->whereDate('date', today())
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $attendance
        ]);
    }

    /**
     * Get all attendance for today
     */
    public function getTodayAttendance()
    {
        $attendance = Attendance::whereDate('date', today())
            ->with('child')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $attendance
        ]);
    }
}