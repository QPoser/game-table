<?php

declare(strict_types=1);

namespace App\Dto\ResponseDto;

abstract class ResponseDTO
{
    abstract public function getStatus(): string;
}