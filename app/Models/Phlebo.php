<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phlebo extends Model
{
    use HasFactory;
    protected $fillable = ['first_name','sirname','last_name','email','phone'];
}
