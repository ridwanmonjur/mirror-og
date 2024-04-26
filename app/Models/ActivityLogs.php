<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    protected $fillable = ['action', 'log'];

    public function subject()
    {
        return $this->morphTo();
    }

    protected $table = 'activity_logs';

}
