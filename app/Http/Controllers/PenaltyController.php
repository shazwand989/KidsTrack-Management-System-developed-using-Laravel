<?php

namespace App\Http\Controllers;

use App\Models\LateCheckoutPenalty;
use App\Models\PenaltySetting;
use App\Services\PenaltyService;
use App\Services\ToyyibPayService;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function __construct(
        protected PenaltyService $penaltyService,
        protected ToyyibPayService $toyyibPay
    ) {}

    // ============================================
    // ADMIN SETTINGS
    // ============================================
    public function settings()
    {
        $settings = $this->penaltyService->getSettings();
        $penalties = LateCheckoutPenalty::with(['child', 'parent'])->latest()->paginate(20);
        return view('admin.penalty-settings', compact('settings', 'penalties'));
    }

    public function saveSettings(Request $request)
    {
        $fields = ['enabled', 'grace_period', 'penalty_amount',
                   'toyyibpay_mode', 'toyyibpay_category', 'toyyibpay_secret',
                   'callback_url', 'return_url'];

        foreach ($fields as $f) {
            PenaltySetting::set($f, $request->input($f, ''));
        }

        return redirect()->back()->with('success', 'Penalty settings saved.');
    }

    // ============================================
    // PARENT PORTAL
    // ============================================
    public function parentIndex(Request $request)
    {
        $user = auth()->user();
        $childIds = $user->children()->pluck('children.id')->toArray();

        $penalties = LateCheckoutPenalty::whereIn('child_id', $childIds)
            ->with(['child.classroom'])
            ->latest()
            ->get();

        $pending = $penalties->where('payment_status', 'pending');
        $paid = $penalties->where('payment_status', 'paid');
        $totalPending = $pending->sum('penalty_amount');
        $totalPaid = $paid->sum('penalty_amount');

        return view('parent.penalties', compact(
            'penalties', 'pending', 'paid', 'totalPending', 'totalPaid'
        ));
    }

    public function payPenalty($id)
    {
        $penalty = LateCheckoutPenalty::with('child')->findOrFail($id);
        if ($penalty->payment_status === 'paid') {
            return back()->with('error', 'Already paid.');
        }

        $result = $this->toyyibPay->createBill($penalty);
        if ($result['success']) {
            return redirect($result['payment_url']);
        }

        return back()->with('error', $result['message'] ?? 'Payment failed.');
    }

    // ============================================
    // TOYYIBPAY CALLBACK
    // ============================================
    public function callback(Request $request)
    {
        $this->toyyibPay->handleCallback($request->all());
        return response('OK');
    }

    // ============================================
    // ADMIN: MANAGE PENALTIES
    // ============================================
    public function markPaid($id)
    {
        $penalty = LateCheckoutPenalty::findOrFail($id);
        $penalty->update(['payment_status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Marked as paid.');
    }

    public function destroy($id)
    {
        LateCheckoutPenalty::findOrFail($id)->delete();
        return back()->with('success', 'Penalty deleted.');
    }
}
