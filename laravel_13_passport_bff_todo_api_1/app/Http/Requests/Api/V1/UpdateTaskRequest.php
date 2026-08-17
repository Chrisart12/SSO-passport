<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|min:3|max:255',
            'description' => 'sometimes|nullable|string|max:2000',
            'is_completed' => 'sometimes|boolean',
            'due_date' => 'sometimes|nullable|date',
        ];
    }
}
