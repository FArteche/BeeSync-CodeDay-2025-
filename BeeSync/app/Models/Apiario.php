<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apiario extends Model
{
    use HasFactory;

    protected $table = 'apiarios';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nome',
        'localizacao',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function colmeias()
    {
        return $this->hasMany(Colmeia::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'apiario_user');
    }

    public function membros()
{
    return $this->belongsToMany(User::class, 'apiario_user');
}
}
