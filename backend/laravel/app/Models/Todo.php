<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'due_date',
        'time_tracked',
        'status',
        'priority',
        'assignee_id',
    ];

    protected $keyType = 'string';
    protected $hidden = ['assignee_id'];
    public $incrementing = false;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}