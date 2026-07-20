<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Attendance;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class QRController extends Controller
{
    /**
     * Generate QR Code using external API
     * POST /api/qr/generate
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|exists:children,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $child = Child::find($request->child_id);
        
        // Generate unique QR data
        $qrData = 'KID-' . str_pad($child->id, 4, '0', STR_PAD_LEFT) . '-' . time() . '-' . substr(md5($child->id . time()), 0, 8);
        
        // Call external QR API
        $qrResponse = Http::get('https://api.qrserver.com/v1/create-qr-code/', [
            'size' => '300x300',
            'data' => $qrData,
            'format' => 'png',
        ]);

        if (!$qrResponse->successful()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate QR Code from external API'
            ], 500);
        }

        // Save QR data
        $child->update([
            'qr_code' => $qrData,
            'qr_code_url' => rtrim(config('app.url'), '/') . '/scan-qr/' . $qrData,
        ]);

        // Return QR Code as base64
        return response()->json([
            'status' => 'success',
            'data' => [
                'child_id' => $child->id,
                'child_name' => $child->name,
                'qr_data' => $qrData,
                'qr_url' => $child->qr_code_url,
                'qr_image' => base64_encode($qrResponse->body()),
            ]
        ]);
    }

    /**
     * Validate QR Code
     * POST /api/qr/validate
     */
    public function validateQR(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $qrData = $request->qr_data;
        
        // Parse QR data
        if (!preg_match('/^KID-(\d{4})-(\d+)-([a-f0-9]{8})$/', $qrData, $matches)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid QR Code format',
                'code' => 'INVALID_FORMAT'
            ], 400);
        }

        $childId = (int)$matches[1];
        $timestamp = (int)$matches[2];
        
        // Check expiry (24 hours)
        if (time() - $timestamp > 86400) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code has expired',
                'code' => 'EXPIRED'
            ], 400);
        }

        // Find child
        $child = Child::with(['parent', 'classroom'])->find($childId);
        
        if (!$child) {
            return response()->json([
                'status' => 'error',
                'message' => 'Child not found',
                'code' => 'CHILD_NOT_FOUND'
            ], 404);
        }

        // Check location (if provided)
        $locationValid = true;
        if ($request->has('latitude') && $request->has('longitude')) {
            $locationValid = $this->checkLocation(
                $request->latitude,
                $request->longitude
            );
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'child' => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'classroom' => $child->classroom->name ?? 'Not assigned',
                    'photo' => $child->photo ? url('storage/' . $child->photo) : null,
                ],
                'parent' => [
                    'name' => $child->parent->name ?? 'N/A',
                    'phone' => $child->parent->phone ?? 'N/A',
                ],
                'location_valid' => $locationValid,
                'timestamp' => now()->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Process attendance (check-in/out)
     * POST /api/qr/attendance
     */
    public function processAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|string',
            'action' => 'required|in:checkin,checkout',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Parse QR data
        if (!preg_match('/^KID-(\d{4})/', $request->qr_data, $matches)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid QR Code'
            ], 400);
        }

        $childId = (int)$matches[1];
        $child = Child::find($childId);
        
        if (!$child) {
            return response()->json([
                'status' => 'error',
                'message' => 'Child not found'
            ], 404);
        }

        // Check today's attendance
        $attendance = Attendance::where('child_id', $childId)
            ->whereDate('date', today())
            ->first();

        $currentTime = now();
        $hour = $currentTime->hour;

        if ($request->action === 'checkin') {
            // Check-in logic
            if ($attendance) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Child already checked-in today',
                    'data' => [
                        'child' => $child->name,
                        'check_in_time' => $attendance->check_in,
                    ]
                ]);
            }

            // Determine status
            if ($hour >= 7 && $hour < 9) {
                $status = 'present';
                $message = 'Check-in successful! (On-time)';
            } elseif ($hour >= 9 && $hour < 12) {
                $status = 'late';
                $message = 'Check-in successful! (Late)';
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Check-in only allowed 7:00 AM - 12:00 PM'
                ], 422);
            }

            $attendance = Attendance::create([
                'child_id' => $childId,
                'date' => today(),
                'check_in' => $currentTime,
                'status' => $status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => [
                    'child' => $child->name,
                    'check_in_time' => $currentTime->format('h:i A'),
                    'status' => $status,
                ]
            ]);

        } else {
            // Check-out logic
            if (!$attendance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Child not checked-in today'
                ], 422);
            }

            if ($attendance->check_out) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Child already checked-out',
                    'data' => [
                        'child' => $child->name,
                        'check_out_time' => $attendance->check_out,
                    ]
                ]);
            }

            // Check-out time: 4:00 PM - 6:00 PM
            if ($hour < 16 || $hour >= 18) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Check-out only allowed 4:00 PM - 6:00 PM'
                ], 422);
            }

            $attendance->update([
                'check_out' => $currentTime,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Check-out successful!',
                'data' => [
                    'child' => $child->name,
                    'check_out_time' => $currentTime->format('h:i A'),
                ]
            ]);
        }
    }

    /**
 * Show kiosk page
 */
public function kiosk()
{
    return view('kiosk.index');
}


    /**
     * Get child info from QR Code
     * GET /api/qr/child/{qr_data}
     */
    public function getChildByQR($qrData)
    {
        $child = Child::where('qr_code', $qrData)->with(['parent', 'classroom'])->first();
        
        if (!$child) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'child' => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'date_of_birth' => $child->date_of_birth,
                    'gender' => $child->gender,
                    'classroom' => $child->classroom->name ?? 'Not assigned',
                ],
                'parent' => [
                    'name' => $child->parent->name ?? 'N/A',
                    'phone' => $child->parent->phone ?? 'N/A',
                ],
                'attendance_today' => Attendance::where('child_id', $child->id)
                    ->whereDate('date', today())
                    ->first(),
            ]
        ]);
    }

    /**
     * Check location against nursery coordinates
     */
    private function checkLocation($lat, $lng)
    {
        // Nursery coordinates (set in .env or database)
        $nurseryLat = env('NURSERY_LATITUDE', 3.123456);
        $nurseryLng = env('NURSERY_LONGITUDE', 101.654321);
        $allowedRadius = env('NURSERY_RADIUS', 100); // meters

        $distance = $this->calculateDistance($lat, $lng, $nurseryLat, $nurseryLng);
        
        return $distance <= $allowedRadius;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}