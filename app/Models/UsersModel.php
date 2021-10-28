<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UsersModel extends Model
{
    /**
     * Table database
     */
    protected $table = 'tbl_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 'email', 'username', 'role_id'
    ];

    protected $hidden = [
        'password', 'created_at', 'updated_at'
    ];
}
