<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class members extends Model
{
    protected $fillable = [
        'verify','password'
    ];
//     protected $guarded = [
//         'id', 'level', 'name', 'email', 'phone',  'twId', 'password', 'right'
//     ];
}