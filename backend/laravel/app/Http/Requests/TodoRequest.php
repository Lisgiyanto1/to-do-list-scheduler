<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        if ($this->isMethod('post')) {

            return [
                'title' => 'required|string',
                'due_date' => 'required|date',
                'status' => 'nullable|string',
                'priority' => 'required|string',
                'assignee_id' => 'nullable|uuid|exists:users,id',
                'estimated_sp' => 'nullable|number',
                'actual_sp' => 'nullable|number',
                'type' => 'nullable|string'
            ];
        }
        return [
            'title' => 'sometimes|string',
            'due_date' => 'sometimes|date',
            'status' => 'nullable|string',
            'priority' => 'sometimes|string',
            'assignee_id' => 'nullable|uuid|exists:users,id',
            'estimated_sp' => 'nullable|number',
            'actual_sp' => 'nullable|number',
            'type' => 'nullable|string'
        ];
    }
}