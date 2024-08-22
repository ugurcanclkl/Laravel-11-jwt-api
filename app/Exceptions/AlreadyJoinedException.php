<?php

namespace App\Exceptions;

use App\Exceptions\Concerns\ErrorStatusCode;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;

class AlreadyJoinedException extends Exception
{
    use ApiResponseTrait;
    protected $errorMessage;
    protected $errorMessageCode       = ErrorStatusCode::ALREADY_JOINED;
    protected $errorMessageStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function construct(): void
    {
        $this->errorMessage = Lang::get('api.already_joined');
        parent::__construct($this->errorMessage, ErrorStatusCode::ALREADY_JOINED);
    }

    public function report(): bool
    {
        return false;
    }

    public function render()
    {
        return $this->error(
            Lang::get('api.already_joined'),
            [
                'status_code' => $this->errorMessageCode,
            ],
            $this->errorMessageStatusCode
        );
    }
}
