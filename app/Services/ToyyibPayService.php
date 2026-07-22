<?php

namespace App\Services;

use App\Models\LateCheckoutPenalty;
use Illuminate\Support\Facades\Http;

class ToyyibPayService
{
    protected string $liveUrl = 'https://toyyibpay.com/api/';
    protected string $devUrl = 'https://dev.toyyibpay.com/api/';
    protected PenaltyService $penaltyService;

    public function __construct(PenaltyService $penaltyService)
    {
        $this->penaltyService = $penaltyService;
    }

    /**
     * Get the base URL based on sandbox/live mode.
     */
    protected function getBaseUrl(): string
    {
        $settings = $this->penaltyService->getSettings();
        $mode = $settings['toyyibpay_mode'] ?? 'sandbox';
        return $mode === 'live' ? $this->liveUrl : $this->devUrl;
    }

    /**
     * Get the payment page URL based on mode.
     */
    protected function getPaymentUrl(string $billCode): string
    {
        $settings = $this->penaltyService->getSettings();
        $mode = $settings['toyyibpay_mode'] ?? 'sandbox';
        $host = $mode === 'live' ? 'toyyibpay.com' : 'dev.toyyibpay.com';
        return "https://{$host}/{$billCode}";
    }

    /**
     * Create a bill on ToyyibPay for a penalty.
     */
    public function createBill(LateCheckoutPenalty $penalty): array
    {
        $settings = $this->penaltyService->getSettings();
        $secret = $settings['toyyibpay_secret'];
        $category = $settings['toyyibpay_category'];

        if (!$secret) {
            return ['success' => false, 'message' => 'ToyyibPay not configured'];
        }

        $child = $penalty->child;
        $ref = 'LCP-' . $penalty->id . '-' . time();

        $response = Http::asForm()->post($this->getBaseUrl() . 'index.php/api/createBill', [
            'userSecretKey' => $secret,
            'categoryCode' => $category,
            'billName' => "Late Pickup Penalty - {$child->name}",
            'billDescription' => "Late checkout on {$penalty->date->format('d M Y')} — {$penalty->late_minutes} minutes late",
            'billPriceSetting' => 0,
            'billPayorInfo' => 1,
            'billAmount' => $penalty->penalty_amount * 100, // ToyyibPay uses cents
            'billReturnUrl' => $settings['return_url'],
            'billCallbackUrl' => $settings['callback_url'],
            'billExternalReferenceNo' => $ref,
            'billTo' => $child->parent->name ?? 'Parent',
            'billEmail' => $child->parent->email ?? '',
            'billPhone' => $child->parent->phone_number ?? '',
        ]);

        $data = $response->json();

        if (!empty($data[0]['BillCode'])) {
            $penalty->update([
                'bill_code' => $data[0]['BillCode'],
            ]);

            return [
                'success' => true,
                'bill_code' => $data[0]['BillCode'],
                'payment_url' => $this->getPaymentUrl($data[0]['BillCode']),
            ];
        }

        return ['success' => false, 'message' => $data['msg'] ?? 'Failed to create bill'];
    }

    /**
     * Handle ToyyibPay callback/webhook.
     */
    public function handleCallback(array $data): bool
    {
        $billCode = $data['billcode'] ?? null;
        $status = $data['status'] ?? null;

        if (!$billCode) return false;

        $penalty = LateCheckoutPenalty::where('bill_code', $billCode)->first();
        if (!$penalty) return false;

        if ($status === '1') {
            $penalty->update([
                'payment_status' => 'paid',
                'transaction_id' => $data['transaction_id'] ?? null,
                'paid_at' => now(),
                'payment_method' => 'ToyyibPay',
            ]);
            return true;
        }

        $penalty->update(['payment_status' => 'failed']);
        return false;
    }
}
