<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'booking_id',
        'recipient',
        'message',
        'provider',
        'status',
        'response',
    ];}
