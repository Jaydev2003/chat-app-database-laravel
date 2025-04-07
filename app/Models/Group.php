<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'group_img', 'created_by'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            $group->id = (string) Str::uuid();
        });
    }

    
    public function users()
{
    return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
}


    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
