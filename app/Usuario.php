<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';


    public function equipes()
    {
        return $this->belongsToMany(Equipe::class, "equipe_usuario", "usuario_id", "equipe_id");
    }
}
