<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'order',
        'column_id',
        'created_by'
    ];

    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    public function createdBy() // Relación con el usuario que creó la tarjeta
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 