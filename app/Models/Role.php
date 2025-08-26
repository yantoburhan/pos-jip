<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'role_feature');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'roles'); // kolom di users = roles (integer)
    }
}
