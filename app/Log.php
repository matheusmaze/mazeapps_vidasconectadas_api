<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        "users_id",
        "controller",
        "uri",
        "parametros",
        "body",
        "metodo"
    ];

    public function users(){
        return $this->belongsTo(User::class, 'users_id');
    }
}
