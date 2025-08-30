<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingUpdateEnglishJourneyLogRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'level_summary' => 'nullable|string',
            'difficulties' => 'nullable|string',
            'confidence_level' => 'nullable|numeric:|between:0,100',
            'ia_summary' => 'nullable|string'
        ];
    }
}
