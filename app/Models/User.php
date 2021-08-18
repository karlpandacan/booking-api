<?php

/**
 * @author: Karl Pandacan
 * @page: User Model
 * @created: 2021-06-01
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class User extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname',
        'username',
        'password',
        'token',
        'token_expired_date',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
        'password',
        'token_expired_date',

    ];

    const PER_PAGES = 25;

    public function scopeLogin($query, $username, $password)
    {
        return $query->where('username', $username)
            ->where('password', md5($password));
    }

    public function scopeFindByToken($query, $requestToken)
    {
        return $query->where('token', $requestToken)
            ->where('token_expired_at', '>=', Carbon::now());
    }
}
