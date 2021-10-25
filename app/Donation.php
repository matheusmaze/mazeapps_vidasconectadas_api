<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use SoftDeletes;

    protected $table = 'donations';

    static public $rules_post = [];
    static public $rules_update = [];

    protected $fillable = [
        "recurrence_day",
        "recurrence_interval",
        "end_recurrence",
        "notification_type",
        "payment_type",
        "fixed_value",
        "users_id"
    ];

    public function users(){
        return $this->belongsTo(User::class, 'users_id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class, 'donations_id');
    }
}
