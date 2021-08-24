<?php

namespace App\Repositories;

use Log;
use StdClass;
use Exception;

use Carbon\Carbon;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends Repository implements UserRepositoryInterface
{
    protected $userRepository;

    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
