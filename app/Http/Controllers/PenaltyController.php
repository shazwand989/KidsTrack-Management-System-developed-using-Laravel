<?php

namespace App\Http\Controllers;

use App\Models\LateCheckoutPenalty;
use App\Models\PenaltySetting;
use App\Models\User;
use App\Services\PenaltyService;
use App\Services\ToyyibPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenaltyController extends Controller
{
    public function __construct(
        protected PenaltyService $penaltyService,
        protected ToyyibPayService $toyyibPay
    ) {}

    // ============================================
    // ADMIN SETTINGS
    // ============================================
    public function fines()
    {
        $allPenalties = LateCheckoutPenalty::with(['child.classroom', 'parent'])->latest()->get();

        $pending = $allPenalties->where('payment_status', 'pending');
        $paid = $allPenalties->where('payment_status', 'paid');

        $totalPending = $pending->sum('penalty_amount');
        $totalPaid = $paid->sum('penalty_amount');
        $totalCollected = $paid->count();

        // Group pending by parent for summary
        $byParent = $pending->groupBy('parent_id')->map(function ($items) {
            $first = $items->first();
            return [
                'parent_name' => $first->parent->name ?? 'Unknown',
                'parent_id' => $first->parent_id,
                'count' => $items->count(),
                'total' => $items->sum('penalty_amount'),
                'penalties' => $items,
            ];
        })->sortByDesc('total');

        return view('admin.fines', compact(
            'pending', 'paid', 'totalPending', 'totalPaid',
            'totalCollected', 'byParent', 'allPenalties'
        ));
    }

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
        /** @var User $user */
        $user = Auth::user();
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

    public function payPenalty(int $id)
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
    public function markPaid(int $id)
    {
        $penalty = LateCheckoutPenalty::findOrFail($id);
        $penalty->update(['payment_status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Marked as paid.');
    }

    public function destroy(int $id)
    {
        LateCheckoutPenalty::findOrFail($id)->delete();
        return back()->with('success', 'Penalty deleted.');
    }
}
