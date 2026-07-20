<?php

namespace App\Models;

/**
 * SecondParent — extends User (normalized into users table with role='parent2')
 */
class SecondParent extends User
{
    protected $table = 'users';
}
