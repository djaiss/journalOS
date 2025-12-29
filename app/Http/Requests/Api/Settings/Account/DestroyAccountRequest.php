<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Settings\Account;

use Illuminate\Foundation\Http\FormRequest;

final class DestroyAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'Please provide a reason for deleting the account.',
            'reason.string' => 'The reason must be a valid string.',
            'reason.min' => 'The reason must be at least :min characters.',
            'reason.max' => 'The reason may not be greater than :max characters.',
        ];
    }
}
