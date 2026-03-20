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

        return Todo::create([
            'title' => $data['title'],
            'assignee_id' => $data['assignee_id'] ?? null,
            'due_date' => $data['due_date'],
            'time_tracked' => $data['time_tracked'],
            'status' => $data['status'],
            'priority' => $data['priority'],
        ]);
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

        // title partial match
        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        // assignee multiple
        if (!empty($filters['assignee'])) {
            $assignees = explode(',', $filters['assignee']);
            $query->whereHas('assignee', function ($q) use ($assignees) {
                $q->whereIn('name', $assignees);
            });
        }

        // due_date range
        if (!empty($filters['start'])) {
            $query->where('due_date', '>=', $filters['start']);
        }
        if (!empty($filters['end'])) {
            $query->where('due_date', '<=', $filters['end']);
        }

        // time_tracked range
        if (isset($filters['min'])) {
            $query->where('time_tracked', '>=', $filters['min']);
        }
        if (isset($filters['max'])) {
            $query->where('time_tracked', '<=', $filters['max']);
        }

        // status multiple
        if (!empty($filters['status'])) {
            $statuses = explode(',', $filters['status']);
            $query->whereIn('status', $statuses);
        }

        // priority multiple
        if (!empty($filters['priority'])) {
            $priorities = explode(',', $filters['priority']);
            $query->whereIn('priority', $priorities);
        }

        return $query->orderBy('due_date')->get();
    }


    public function getChartData(string $type): array
    {
        switch ($type) {
            case 'status':
                $data = Todo::select('status')
                    ->selectRaw('count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();

                // Pastikan semua status ada meski 0
                return [
                    'status_summary' => array_merge([
                        'pending' => 0,
                        'open' => 0,
                        'in_progress' => 0,
                        'completed' => 0,
                    ], $data)
                ];

            case 'priority':
                $data = Todo::select('priority')
                    ->selectRaw('count(*) as total')
                    ->groupBy('priority')
                    ->pluck('total', 'priority')
                    ->toArray();

                return [
                    'priority_summary' => array_merge([
                        'low' => 0,
                        'medium' => 0,
                        'high' => 0,
                    ], $data)
                ];

            case 'assignee':
                $todos = Todo::with('assignee')->get();

                $summary = [];

                foreach ($todos as $todo) {
                    $name = $todo->assignee?->name ?? 'Unassigned';

                    if (!isset($summary[$name])) {
                        $summary[$name] = [
                            'total_todos' => 0,
                            'total_pending_todos' => 0,
                            'total_timetracked_completed_todos' => 0,
                        ];
                    }

                    $summary[$name]['total_todos'] += 1;

                    if ($todo->status === 'pending') {
                        $summary[$name]['total_pending_todos'] += 1;
                    }

                    if ($todo->status === 'completed') {
                        $summary[$name]['total_timetracked_completed_todos'] += $todo->time_tracked;
                    }
                }

                return ['assignee_summary' => $summary];

            default:
                throw new \InvalidArgumentException("Invalid chart type: $type");
        }
    }
}