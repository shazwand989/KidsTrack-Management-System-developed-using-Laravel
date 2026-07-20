<?php

namespace App\Models;

/**
 * Guardian — extends User (normalized into users table with role='guardian')
 */
class Guardian extends User
{
    protected $table = 'users';
}
