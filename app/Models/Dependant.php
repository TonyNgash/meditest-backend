<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependant extends Model
{
    use HasFactory;
    protected $table = "dependant";
    protected $fillable = [
        'first_name',
        'sirname',
        'last_name',
        'gender',
        'address',
        'dob',
        'user_id',
        'relationship'
    ];
}
