<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedPatient extends Model
{
    use HasFactory;

    protected $table = 'booked_patients';
    protected $fillable=[
        'booking_id','dependant_id'
    ];
}
