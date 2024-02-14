<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    public $timestamps = false;
    protected $table = 'wx_vote';
}
