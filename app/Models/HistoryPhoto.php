<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryPhoto extends Model
{
    protected $fillable = [
        'fasilitas_history_id',
        'foto'
    ];
}