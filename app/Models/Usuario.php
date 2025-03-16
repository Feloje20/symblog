<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Usuario extends Eloquent{
    protected $table = 'users';
    protected $fillable = ['id', 'user', 'password', 'email', 'profile', 'created_at', 'updated_at'];
}