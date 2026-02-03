<?php

namespace App\Http\Requests;

use App\Http\Application\Events\IngestEvent\IngestEventCommand;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IngestEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'external_id' => 'required|string',
            'source' => 'required|string',
            'type' => 'required|string',
            'payload' => 'required|array',
            'occurred_at' => 'required|date',
        ];
    }

    public function toCommand(): IngestEventCommand
    {
        return new IngestEventCommand(
            external_id: $this->input('external_id'),
            source: $this->input('source'),
            type: $this->input('type'),
            payload: $this->input('payload'),
            occurred_at: $this->input('occurred_at'),
        );
    }
}
