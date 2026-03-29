<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TodoRequest;
use App\Http\Requests\ChartRequest;
use App\Services\TodoService;
use App\Exports\TodoExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    public function __construct(private TodoService $todoService)
    {
    }
    public function index(Request $request): JsonResponse
    {
        // 1. Ambil semua parameter yang dikirim dari useTodosQuery.ts
        // Sesuaikan key-nya dengan yang dikirim dari Frontend
        $filters = $request->only([
            'search',      // Dari store.search
            'assignee_id', // Dari store.selectedAssigneeId
            'status',
            'priority',
            'sort_by',     // Untuk sorting dinamis
            'sort_order',  // asc atau desc
            'per_page'     // Untuk pagination dinamis
        ]);

        // 2. Panggil Service (Pastikan Service Anda mendukung pagination)
        // Gunakan paginate() di dalam service/repository agar mengembalikan LengthAwarePaginator
        $todos = $this->todoService->filterTodos($filters);

        return response()->json([
            'success' => true,
            'message' => 'Todos retrieved successfully',
            'data' => $todos // Ini akan berisi { current_page, data: [...], total, dll }
        ]);
    }

    public function store(TodoRequest $request): JsonResponse
    {
        $todo = $this->todoService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Todo created successfully',
            'data' => $todo
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        // Menggunakan service getById agar fitur Cache terimplementasi
        $todos = $this->todoService->getById($id);

        return response()->json([
            'success' => true,
            'data' => $todos->items(),
            'meta' => [
                'current_page' => $todos->currentPage(),
                'last_page' => $todos->lastPage(),
                'per_page' => $todos->perPage(),
                'total' => $todos->total()
            ]
        ]);
    }

    public function update(TodoRequest $request, string $id): JsonResponse
    {
        try {
            // Cek apa isi validated data
            // \Log::info($request->validated()); 

            $todo = $this->todoService->update($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Todo updated successfully',
                'data' => $todo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(), // Pesan error asli akan muncul di sini
                'trace' => $e->getTrace()[0]   // Menunjukkan baris mana yang error
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $this->todoService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Todo deleted successfully'
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['title', 'assignee', 'start', 'end', 'min', 'max', 'status', 'priority']);
        $todos = $this->todoService->filterTodos($filters);

        return Excel::download(new TodoExport($todos), 'todo_report.xlsx');
    }

    public function chart(ChartRequest $request): JsonResponse
    {
        // Mengambil type dari request (misal: 'status' atau 'priority')
        $type = $request->validated('type');

        // Memanggil service yang sudah kita buat sebelumnya
        $data = $this->todoService->getChartSummary($type);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}