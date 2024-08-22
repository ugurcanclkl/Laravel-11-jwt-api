<?php

namespace App\Exceptions;

use App\Exceptions\Concerns\ErrorStatusCode;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;

class DeniedException extends Exception
{
    use ApiResponseTrait;
    protected $errorMessage;
    protected $errorMessageCode       = ErrorStatusCode::DENIED;
    protected $errorMessageStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function construct(): void
    {
        $this->errorMessage = Lang::get('api.denied');
        parent::__construct($this->errorMessage, ErrorStatusCode::DENIED);
    }

    public function report(): bool
    {
        return false;
    }

    public function render()
    {
        return $this->error(
            Lang::get('api.denied'),
            [
                'status_code' => $this->errorMessageCode,
            ],
            $this->errorMessageStatusCode
        );
    }
}
