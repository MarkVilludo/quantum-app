<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileAppRelease extends Model
{
    protected $fillable = [
        'version_name',
        'version_code',
        'disk_path',
        'original_filename',
        'size_bytes',
        'uploaded_by',
        'is_current',
    ];

    protected $casts = [
        'version_code' => 'integer',
        'size_bytes' => 'integer',
        'is_current' => 'boolean',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
