<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';

    static public $rules_post = [
        'value' => 'required',
        'status' => 'required',
    ];
    static public $rules_update = [];

    protected $fillable = [
        "value",
        "status",
        "payment_voucher",
        "donations_id",
        "txid",
        "data_pagamento",
        "pix"
    ];

    public function donations(){
        return $this->belongsTo(Donation::class, 'donations_id');
    }
}
