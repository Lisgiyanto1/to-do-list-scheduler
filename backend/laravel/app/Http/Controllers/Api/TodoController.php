<?php

namespace App\Http\Controllers\Api;

use App\Enums\ChartType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChartRequest;
use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use App\Services\ChartService;
use App\Services\TodoService;
use App\Exports\TodoExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function __construct(private TodoService $todoService)
    {
    }

    public function store(TodoRequest $request)
    {
        $todo = $this->todoService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Todo created successfully',
            'data' => $todo
        ], 201);
    }

    public function show(string $id)
    {
        $todo = Todo::with('assignee')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $todo
        ]);
    }

    public function update(TodoRequest $request, string $id)
    {
        $todo = $this->todoService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Todo updated successfully',
            'data' => $todo
        ]);
    }

    public function destroy(string $id)
    {
        $this->todoService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Todo deleted successfully'
        ]);
    }

    public function index()
    {
        $todos = $this->todoService->list();

        return response()->json([
            'success' => true,
            'data' => $todos
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['title', 'assignee', 'start', 'end', 'min', 'max', 'status', 'priority']);

        $todos = $this->todoService->filterTodos($filters);

        return Excel::download(new TodoExport($todos), 'todo_report.xlsx');
    }

    public function chart(ChartRequest $request, ChartService $service)
    {
        $type = ChartType::from($request->validated('type'));

        $data = $service->getSummary($type);

        return response()->json([
            $type->responseKey() => $data
        ]);
    }
}