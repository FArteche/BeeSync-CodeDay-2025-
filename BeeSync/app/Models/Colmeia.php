<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colmeia extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'colmeias';
    protected $fillable = [
        'identificacao',
        'apiario_id',
    ];

    public function apiario()
    {
        return $this->belongsTo(Apiario::class);
    }

    public function inspecoes()
    {
        return $this->hasMany(Inspecao::class);
    }
}
