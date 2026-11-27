<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerPayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'partner_share_id' => 'required|exists:partner_shares,id',
            'payout_date'      => 'required|date',
            'amount'           => 'required|numeric|min:0.01',
            'payment_mode_id'  => 'nullable|exists:payment_modes,id',
            'payment_mode'     => 'nullable|string|max:50',
            'reference_no'     => 'nullable|string|max:150',
            'remarks'          => 'nullable|string|max:1000',
        ];
    }
}
