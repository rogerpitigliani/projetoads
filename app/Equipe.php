<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    protected $table = 'equipe';


    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'equipe_usuario', 'equipe_id', 'usuario_id');
    }
}
