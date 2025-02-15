<?php

namespace Tests\Feature\Traits;

use App\Models\Role;

trait WithRoles
{
    protected function setUpRoles()
    {
        Role::create([
            'id' => 2,
            'role_name' => 'seller'
        ]);
    }
} 