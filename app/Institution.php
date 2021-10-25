<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{

    use SoftDeletes;

    protected $table = 'institutions';

    static public $rules_post = [];
    static public $rules_update = [];

    protected $fillable = [
        "name",
        "code",
        "logo",
        "cnpj",
        "phone_number",
        "redirect_link",
        "email"
    ];

    public function buys(){
        return $this->hasMany(Buy::class, 'institutions_id');
    }
}
