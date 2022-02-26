<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\WorkingDay;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'total_paga',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * Get the working days of the user.
     *
     * @return HasMany
     */
    public function workingDays(): HasMany
    {
        return $this->hasMany(WorkingDay::class);
    }
    
    /**
     * @return int
     */
    public function getNormalWorkingHoursAttribute(): int
    {
        return $this->workingDays->sum('hours') - $this->getOvertimeWorkingHoursAttribute();
    }
    
    /**
     * @return int
     */
    public function getOvertimeWorkingHoursAttribute(): int
    {
        return $this->workingDays()->where('hours', '>', 8)->sum(DB::raw('hours - 8'));
    }
}
