<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki izin untuk membuat request ini.
     */
    public function authorize(): bool
    {
        // Pastikan ini true agar request tidak tertolak secara otomatis
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     */
    public function rules(): array
    {
        // Aturan Dasar (Base Rules) yang umum digunakan
        $baseRules = [
            'status' => 'nullable|string',
            'assignee_id' => 'nullable|uuid|exists:users,id',
            'estimated_sp' => 'nullable|integer',
            'actual_sp' => 'nullable|integer',
            'type' => 'nullable|string',
        ];

        // 1. Aturan untuk CREATE (POST)
        if ($this->isMethod('post')) {
            return array_merge($baseRules, [
                'title' => 'required|string|max:255',
                'due_date' => 'required|date',
                'priority' => 'required|string',
            ]);
        }

        // 2. Aturan untuk UPDATE (PUT/PATCH)
        // Menggunakan 'sometimes' di depan semua field agar Laravel hanya 
        // memvalidasi field yang benar-benar ada di dalam payload request.
        return [
            'title' => 'sometimes|string|max:255',
            'due_date' => 'sometimes|date',
            'status' => 'sometimes|nullable|string',
            'priority' => 'sometimes|string',
            'assignee_id' => 'sometimes|nullable|uuid|exists:users,id',
            'estimated_sp' => 'sometimes|nullable|integer',
            'actual_sp' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|string',
            'time_tracked' => 'sometimes|nullable|integer',
        ];
    }

    /**
     * Kustomisasi pesan error (Optional)
     * Sangat membantu untuk debugging di Frontend jika terjadi Error 422
     */
    public function messages(): array
    {
        return [
            'estimated_sp.integer' => 'Nilai Estimated SP harus berupa angka bulat.',
            'actual_sp.integer' => 'Nilai Actual SP harus berupa angka bulat.',
            'due_date.date' => 'Format tanggal tidak valid.',
            'assignee_id.exists' => 'User yang dipilih tidak ditemukan.',
        ];
    }
}