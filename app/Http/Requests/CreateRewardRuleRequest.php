<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRewardRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'merchant_id' => 'required|exists:merchants,id',
            'name' => 'required|string',
            'event_type' => 'required|string',
            'reward_type' => 'required|string',
            'reward_value' => 'required|numeric',
            'is_active' => 'required|boolean',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date',
            'conditions' => 'required|array',
            'priority' => 'nullable|numeric',
        ];
    }
}
