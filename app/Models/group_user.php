<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class group_user extends Model
{
    protected $fillable = ['group_id', 'user_id'];
    protected $table = 'group_user';


    public function user() {
        return $this->belongsTo(User::class);
    }
    
}
