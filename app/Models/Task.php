<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
        use HasFactory;

    protected $fillable = [
        'task_name', 
        'project_id', 
        'user_id', 
        'status', 
        'priority', 
        'due_date'
    ];

    // المهمة تنتمي لمشروع واحد (BelongsTo)
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // المهمة تنتمي لمستخدم واحد مسؤول عنها (BelongsTo)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}