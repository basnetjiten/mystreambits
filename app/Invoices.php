<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoices extends  Model
{
    protected $fillable = [
        'id', 'user_id', 'amount','invoice_id','commission_amount','updated_at','created_at','invoice_status'
    ];


}