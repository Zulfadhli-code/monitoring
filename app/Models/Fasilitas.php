<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'lokasi',
        'kategori',
        'status',
        'detail',
        'foto',
        'keterangan',
        'latitude',
    'longitude'
    ];

    public function histories()
{
    return $this->hasMany(FasilitasHistory::class);
}
public function updater()
{
    return $this->belongsTo(User::class, 'updated_by');
}
public function photos()
{
    return $this->hasMany(FasilitasPhoto::class);
}
}