<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspecao extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'inspecaos';
    protected $fillable = [
        'data_inspecao',
        'viu_rainha',
        'nivel_populacao', //1 - 5
        'reservas_mel', //1 - 5
        'sinais_parasitas',
        'observacoes',
        'colmeia_id',
        'user_id',
    ];

    public function colmeia()
    {
        return $this->belongsTo(Colmeia::class);
    }

    public function inspetor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
