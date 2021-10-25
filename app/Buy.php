<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buy extends Model
{
    use SoftDeletes;

    protected $table = 'buys';

    static public $rules_post = [
        'description' => 'required|string',
        'purchase_voucher' => 'required',
        'value' => 'required'
    ];
    static public $rules_update = [];

    protected $fillable = [
        "description",
        "value",
        "purchase_voucher",
        "users_id",
        "institutions_id",
        "status",
        "observation",
        "created_at",
        "updated_at"
    ];

    public function users(){
        return $this->belongsTo(User::class, 'users_id');
    }

    public function institutions(){
        return $this->belongsTo(Institution::class, 'institutions_id');
    }
}
