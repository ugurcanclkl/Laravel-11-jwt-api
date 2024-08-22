<?php

namespace App\Exceptions\Concerns;

use App\Models\Concerns\Support\Foundable;

class ErrorStatusCode
{
    use Foundable;
    public const CREDENTIALS_DO_NOT_EXIST       = 1001;
    public const UNAUTHORIZED                   = 1002;
    public const USER_IS_NOT_ADMIN              = 1003;
    public const DENIED                         = 1004;
    public const ALREADY_WAITING                = 1005;
    public const ALREADY_JOINED                 = 1006;
    public static array $list                   = [
        'credentials_do_not_exist'                 => self::CREDENTIALS_DO_NOT_EXIST,
        'you_are_not_allowed_to_view_this_content' => self::UNAUTHORIZED,
        'user_is_not_admin'                        => self::USER_IS_NOT_ADMIN,
        'denied'                                   => self::DENIED,
        'already_waiting'                          => self::ALREADY_WAITING,
        'already_joined'                           => self::ALREADY_JOINED,
    ];
}
