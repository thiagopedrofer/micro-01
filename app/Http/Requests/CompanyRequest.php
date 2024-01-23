<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function rules(): array
    {
        $uuid = $this->route('uuid');

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', Rule::unique('companies', 'name')->ignore($uuid, 'uuid')],
            'whatsapp' => ['required', Rule::unique('companies', 'whatsapp')->ignore($uuid, 'uuid')],
            'email' => ['required', Rule::unique('companies', 'email')->ignore($uuid, 'uuid')],
            'facebook' => ['nullable', Rule::unique('companies', 'facebook')->ignore($uuid, 'uuid')],
            'phone' => ['nullable', Rule::unique('companies', 'phone')->ignore($uuid, 'uuid')],
            'instagram' => ['nullable', Rule::unique('companies', 'instagram')->ignore($uuid, 'uuid')],
            'youtube' => ['nullable', Rule::unique('companies', 'youtube')->ignore($uuid, 'uuid')]
        ];
    }
}
