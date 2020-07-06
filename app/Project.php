<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'owner_id', 'title', 'description'
    ];

    public function owner()
    {
        return $this->belongsTo('App\User');
    }
}
