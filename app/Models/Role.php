<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use \App\Traits\Auditable;

    protected $fillable = ['name', 'guard_name', 'display_name'];
}
