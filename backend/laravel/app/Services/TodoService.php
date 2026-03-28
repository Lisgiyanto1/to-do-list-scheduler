<?php

namespace App\Services;

use App\Models\Todo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

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
        // Menggunakan cache agar lebih cepat, pastikan relasi ikut terbawa
        return Cache::remember("todo:$id", 60, function () use ($id) {
            return Todo::with('assignee:id,name')->findOrFail($id);
        });
    }

    public function update(string $id, array $data): Todo
    {
        $todo = Todo::findOrFail($id);
        $todo->update($data);

        Cache::forget("todo:$id");
        return $todo->load('assignee:id,name');
    }

    public function delete(string $id): bool
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        Cache::forget("todo:$id");
        return true;
    }

    public function filterTodos(array $filters)
    {
        $query = Todo::query()
            ->with([
                'assignee' => function ($query) {
                    $query->select('id', 'name')
                        ->with('developer:id,user_id,profile_picture,status_akun');
                }
            ])
            ->select([
                'id',
                'title',
                'assignee_id',
                'due_date',
                'time_tracked',
                'status',
                'priority',
                'created_at'
            ]);

        // 🔍 SEARCH (Menghilangkan strlen agar search box yang dihapus/backspace terdeteksi)
        if (!empty($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        // 👤 FILTER ASSIGNEE
        if (!empty($filters['assignee_id'])) {
            $assignee = $filters['assignee_id'];
            if ($assignee === 'me') {
                $query->where('assignee_id', auth()->id());
            } elseif ($assignee === 'unassigned' || $assignee === 'none') {
                $query->whereNull('assignee_id');
            } else {
                $query->where('assignee_id', $assignee);
            }
        }

        // 📊 FILTER STATUS & PRIORITY
        if (!empty($filters['status'])) {
            $query->whereIn('status', explode(',', $filters['status']));
        }
        if (!empty($filters['priority'])) {
            $query->whereIn('priority', explode(',', $filters['priority']));
        }

        // 📅 DATE RANGE & TIME
        if (!empty($filters['start']))
            $query->whereDate('due_date', '>=', $filters['start']);
        if (!empty($filters['end']))
            $query->whereDate('due_date', '<=', $filters['end']);
        if (isset($filters['min']))
            $query->where('time_tracked', '>=', (int) $filters['min']);
        if (isset($filters['max']))
            $query->where('time_tracked', '<=', (int) $filters['max']);

        // 🔃 SORTING (SAFE)
        $allowedSortColumns = ['created_at', 'title', 'priority', 'status', 'time_tracked', 'due_date'];
        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSortColumns) ? $filters['sort_by'] : 'created_at';
        $sortOrder = strtolower($filters['sort_order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortOrder);

        // 📄 PAGINATION
        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 10;
        return $query->paginate(min($perPage, 50));
    }

    /**
     * Logic Chart dipusatkan di sini agar Controller tetap bersih
     */
    public function getChartSummary(string $type): array
    {
        $allowed = ['status', 'priority'];

        if (!in_array($type, $allowed)) {
            throw new \InvalidArgumentException('Invalid chart type');
        }

        $cacheKey = "chart_data_{$type}";

        return Cache::remember($cacheKey, 60, function () use ($type) {
            $column = $type; // status atau priority
            return [
                "{$type}_summary" => Todo::selectRaw("$column, COUNT(*) as total")
                    ->groupBy($column)
                    ->pluck('total', $column)
                    ->toArray()
            ];
        });
    }
}