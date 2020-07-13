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

    public function scopeFilter($query, $filter)
    {
        return $filter->apply($query);
    }

    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    public function members()
    {
        return $this->belongsToMany('App\User', 'project_user', 'project_id', 'user_id');
    }

    public function isPublic()
    {
        return $this->visibility === 'public';
    }
}
