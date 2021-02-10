<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedService extends Model
{
    use HasFactory;

    protected $table = 'booked_services';
    protected $fillable = [
        'booking_id','service_id'
    ];

}
