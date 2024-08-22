<?php

namespace App\Models\Concerns;

use App\Models\Concerns\Support\Concern;
use App\Models\Concerns\Support\Foundable;

class RoomUserStatus extends Concern
{
    use Foundable;

    public const DENIED  = 1;
    public const WAITING = 2;
    public const ACCEPTED = 3;
    public const CANCELED = 4;

    public static array $list = [
        'denied'   => self::DENIED,
        'waiting'  => self::WAITING,
        'accepted' => self::ACCEPTED,
        'canceled' => self::CANCELED,
    ];
}