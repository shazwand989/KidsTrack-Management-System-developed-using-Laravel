<?php

namespace App\Models;

/**
 * ParentModel — extends User (normalized into users table with role='parent1')
 */
class ParentModel extends User
{
    protected $table = 'users';
}
