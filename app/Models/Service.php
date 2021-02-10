<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';
    protected $fillable = ['test_name','test_desc','test_price','image_path','creator_job_id','status'];


}
