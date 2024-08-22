<?php

namespace App\Exceptions;

use App\Exceptions\Concerns\ErrorStatusCode;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;

class NotExistsException extends Exception
{
    use ApiResponseTrait;
    protected $errorMessage;
    protected $errorMessageCode       = ErrorStatusCode::CREDENTIALS_DO_NOT_EXIST;
    protected $errorMessageStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function construct(): void
    {
        $this->errorMessage = Lang::get('api.does_not_exists');
        parent::__construct($this->errorMessage, ErrorStatusCode::CREDENTIALS_DO_NOT_EXIST);
    }

    public function report(): bool
    {
        return false;
    }

    public function render()
    {
        return $this->error(
            Lang::get('api.does_not_exists'),
            [
                'status_code' => $this->errorMessageCode,
            ],
            $this->errorMessageStatusCode
        );
    }
}
