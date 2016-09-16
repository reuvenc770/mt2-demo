<?php

namespace App\Models;
use Cartalyst\Sentinel\Users\EloquentUser as SentinelUser;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends SentinelUser implements CanResetPassword{

    use \Illuminate\Auth\Passwords\CanResetPassword;
    protected $fillable = [
        'email',
        'username',
        'password',
        'last_name',
        'first_name',
        'permissions',
    ];

    protected $loginNames = ['email', 'username'];



}