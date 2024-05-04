<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    protected $fillable = ['action', 'subject_id', 'subject_type', 'object_id', 'object_type', 'log'];

    public function subject()
    {
        return $this->morphTo();
    }

    protected $table = 'activity_logs';

}
