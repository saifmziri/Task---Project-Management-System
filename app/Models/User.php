<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',      // تم تعديلها لتوافق الميجريشن
        'email',
        'phone_number',   // تم إضافتها
        'password',
        'role_id',        // تم إضافتها لربط العلاقات
        'status',         // تم إضافتها لحالة الحساب
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // المستخدم ينتمي لدور معين (BelongsTo)
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // المستخدم لديه مهام كثيرة مكلف بها (HasMany)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}