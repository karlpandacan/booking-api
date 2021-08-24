<?php

namespace App\Repositories;

use Log;
use StdClass;
use Exception;

use App\Models\Room;
use App\Repositories\Interfaces\RoomRepositoryInterface;

class RoomRepository extends Repository implements RoomRepositoryInterface
{

    protected $room;

    public function __construct(Room $room)
    {
        parent::__construct($room);
    }
}
