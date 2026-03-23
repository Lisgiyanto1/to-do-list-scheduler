<?php

namespace App\Enums;

enum ChartType: string
{
    case STATUS = 'status';
    case PRIORITY = 'priority';

    public function column(): string
    {
        return match ($this) {
            self::STATUS => 'status',
            self::PRIORITY => 'priority',
        };
    }

    public function default(): array
    {
        return match ($this) {
            self::STATUS => [
                'pending' => 0,
                'open' => 0,
                'in_progress' => 0,
                'completed' => 0,
            ],
            self::PRIORITY => [
                'low' => 0,
                'medium' => 0,
                'high' => 0,
            ],
        };
    }

    public function responseKey(): string
    {
        return $this->value . '_summary';
    }
}