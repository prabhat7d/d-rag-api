<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class AskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2'],
            'top_k' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }
}
