<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Message;

class Conversation extends Model
{
    use HasFactory;
    protected $table = "conversations";
    protected $fillable = ['status', 'user1_id', 'user2_id', 'initiator_id', 'created_at', 'updated_at'];

    public function messages() {
        return $this->hasMany(Message::class, 'conversation_id', 'id');
    } 
}
