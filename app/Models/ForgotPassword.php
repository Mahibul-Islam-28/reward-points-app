<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForgotPassword extends Model
{
    public $timestamps = false;
    protected $table = 'wx_forgot_password';
}
