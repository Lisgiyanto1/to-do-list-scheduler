<?php

namespace App\Services;

use App\Models\Todo;
use Illuminate\Support\Facades\Cache;

class TodoService
{
    public function create(array $data): Todo
    {
        $data['status'] = $data['status'] ?? 'pending';
        $data['priority'] = $data['priority'] ?? 'medium';
        $data['time_tracked'] = $data['time_tracked'] ?? 0;

        $todo = Todo::create([
            'title' => $data['title'],
            'assignee_id' => $data['assignee_id'] ?? null,
            'due_date' => $data['due_date'],
            'time_tracked' => $data['time_tracked'],
            'status' => $data['status'],
            'priority' => $data['priority'],
        ]);

        return $todo->load('assignee:id,name');
    }

    public function getById(string $id): Todo
    {
        return Cache::remember("todo:$id", 60, function () use ($id) {
            return Todo::findOrFail($id);
        });
    }

    public function update(string $id, array $data): Todo
    {
        $todo = Todo::findOrFail($id);
        $todo->update($data);
        Cache::forget("todo:$id");
        return $todo;
    }

    public function delete(string $id): bool
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();
        Cache::forget("todo:$id");
        return true;
    }

    public function list(): \Illuminate\Database\Eloquent\Collection
    {
        return Todo::orderBy('due_date', 'asc')->get();
    }


    public function filterTodos(array $filters)
    {
        $query = Todo::with('assignee');

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }
        if (!empty($filters['assignee'])) {
            $assignees = explode(',', $filters['assignee']);
            $query->whereHas('assignee', function ($q) use ($assignees) {
                $q->whereIn('name', $assignees);
            });
        }

        if (!empty($filters['start'])) {
            $query->where('due_date', '>=', $filters['start']);
        }
        if (!empty($filters['end'])) {
            $query->where('due_date', '<=', $filters['end']);
        }

        if (isset($filters['min'])) {
            $query->where('time_tracked', '>=', $filters['min']);
        }
        if (isset($filters['max'])) {
            $query->where('time_tracked', '<=', $filters['max']);
        }

        if (!empty($filters['status'])) {
            $statuses = explode(',', $filters['status']);
            $query->whereIn('status', $statuses);
        }

        if (!empty($filters['priority'])) {
            $priorities = explode(',', $filters['priority']);
            $query->whereIn('priority', $priorities);
        }

        return $query->orderBy('due_date')->get();
    }


    public function getChartData(string $type): array
    {
        $allowed = ['status', 'priority'];

        if (!in_array($type, $allowed)) {
            throw new \InvalidArgumentException('Invalid chart type');
        }

        $cacheKey = "chart_data_{$type}";

        return Cache::remember($cacheKey, 60, function () use ($type) {
            if ($type === 'status') {
                return [
                    'status_summary' => Todo::selectRaw('status, COUNT(*) as total')
                        ->groupBy('status')
                        ->pluck('total', 'status')
                        ->toArray()
                ];
            }

            return [
                'priority_summary' => Todo::selectRaw('priority, COUNT(*) as total')
                    ->groupBy('priority')
                    ->pluck('total', 'priority')
                    ->toArray()
            ];
        });
    }
}