<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{

    public function rules(): array
    {
        $url = $this->route('url');

        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:150',
                Rule::unique('categories', 'title')->ignore($url, 'url'),
            ],
            'description' => ['required', 'string', 'min:3', 'max:255']
        ];
    }
}
