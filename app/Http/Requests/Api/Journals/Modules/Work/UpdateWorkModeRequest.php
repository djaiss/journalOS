<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Journals\Modules\Work;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateWorkModeRequest extends FormRequest
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
            'work_mode' => ['required', 'string', 'in:on-site,remote,hybrid'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'work_mode.required' => 'The work mode field is required.',
            'work_mode.string' => 'The work mode must be a string.',
            'work_mode.in' => 'The work mode must be on-site, remote, or hybrid.',
        ];
    }
}
