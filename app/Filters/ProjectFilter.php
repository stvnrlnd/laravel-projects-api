<?php

namespace App\Filters;

use App\User;

class ProjectFilter extends Filter
{
    protected $filters = ['by'];

    protected function by($id)
    {
        $user = User::where('id', $id)->firstOrFail();

        return $this->builder->where('owner_id', $user->id);
    }
}
