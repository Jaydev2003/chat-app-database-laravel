<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Message extends Model
{
    use HasUuids;
    protected $casts = [
        'receiver_id' => 'string', 
    ];
    protected $fillable = ['sender_id', 'receiver_id', 'group_id', 'message', 'file_path', 'file_type'];
    protected $table = 'messages';

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

}
