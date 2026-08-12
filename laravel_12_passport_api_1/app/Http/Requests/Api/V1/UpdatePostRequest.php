<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize()
    {
        return true; // You can add authorization logic here
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
        ];
    }
}