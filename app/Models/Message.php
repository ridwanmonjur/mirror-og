<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $connection = "mysql";

    protected $fillable = ['user_id', 'conversation_id', 'reply_id', 'text', 'read_at', 'created_at', 'updated_at', 'expiry_date', 'text'];

    protected $table = "messages";
}
