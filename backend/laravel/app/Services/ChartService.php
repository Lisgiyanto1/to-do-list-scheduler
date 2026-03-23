<?php

namespace App\Services;

use App\Enums\ChartType;
use Illuminate\Support\Facades\DB;

class ChartService
{
    public function getSummary(ChartType $type): array
    {
        $column = $type->column();

        $results = DB::table('todos')
            ->select($column, DB::raw('COUNT(*) as total'))
            ->groupBy($column)
            ->pluck('total', $column);

        return array_merge(
            $type->default(),
            $results->toArray()
        );
    }
}