<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalPassword extends Model
{
    protected $table = 'csv_passwords';

    public $fillable = ['password'];

    public $timestamps = null;
}
