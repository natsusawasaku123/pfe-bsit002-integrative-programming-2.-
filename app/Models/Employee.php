<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'department_id',
        'position',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}