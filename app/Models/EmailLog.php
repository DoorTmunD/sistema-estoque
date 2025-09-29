<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'to',
        'subject',
        'status',
        'error_message',
        'sent_at'
    ];
    public $timestamps = true;
}