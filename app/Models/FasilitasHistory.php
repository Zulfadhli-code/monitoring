<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasilitasHistory extends Model
{
    protected $fillable = [

    'fasilitas_id',
    'user_id',
    'status_from',
    'status_to',
    'keterangan',
    'foto'

];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }
    public function photos()
{
    return $this->hasMany(HistoryPhoto::class);
}
}