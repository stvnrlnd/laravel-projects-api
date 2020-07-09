<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id', 'title', 'description'
    ];

    public function isPublic()
    {
        return $this->visibility === 'public';
    }

    public function owner()
    {
        return $this->belongsTo('App\User');
    }
}
