<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'year_id',
    ];

    // Define the belongsTo relationship for the year
    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }
}
