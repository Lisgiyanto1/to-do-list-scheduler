<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ChartType;

class ChartRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(ChartType::class)],
        ];
    }
}