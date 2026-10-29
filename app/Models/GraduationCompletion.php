<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraduationCompletion extends Model
{
    protected $fillable = [
        'user_id',
        'certificate_code',
        'graduated_at',
    ];

    protected $casts = [
        'graduated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
