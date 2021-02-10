<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Service;

class Booking extends Model
{
    use HasFactory;
    protected $table = "booking";
    protected $fillable = [
        'user_id',
        'self',
        'paid',
        'scheduled_date',
        'scheduled_time',
        'total_amount',
        'old_new',
        'seen',
        'status',
        'phone',
        'address_desc',
        'lat',
        'lon',
        'locality',
        'admin_area',
        'sub_admin_area'
    ];
    /**
     * attributes that should be added by default
     *
     */
    protected $attributes = [
        'seen' => false,
        'old_new'=>true,
        'status'=>'pending'
    ];

    /**
     * The attributes that has json type.
     *
     * @var array
     */
    protected $casts = [
        'service_id' => 'array','dependant_id' => 'array'
    ];

    /**
     * let the booking model know that it belongs to the user model
     */

    public function user(){
        return $this->hasMany(User::class);
    }




}
