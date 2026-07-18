<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'start_date', 'due_date'];

    // المشروع الواحد يحتوي على مهام كثيرة (One-to-Many)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}