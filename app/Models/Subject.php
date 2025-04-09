<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

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
