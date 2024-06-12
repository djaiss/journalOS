<?php

namespace App\Services;

abstract class BaseService
{
    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @param  array<string, string>  $data
     */
    public function valueOrNull(array $data, string $index): ?string
    {
        if (empty($data[$index])) {
            return null;
        }

        return $data[$index] == '' ? null : $data[$index];
    }
}
