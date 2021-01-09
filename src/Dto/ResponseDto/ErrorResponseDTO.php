<?php

declare(strict_types=1);

namespace App\Dto\ResponseDto;

use Symfony\Component\Serializer\Annotation\Groups;

final class ErrorResponseDTO extends ResponseDTO
{
    private const STATUS = 'error';

    /**
     * @Groups({"Api"})
     */
    private string $message;

    /**
     * @Groups({"Api"})
     */
    private int $code;

    public function __construct(string $message, int $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @Groups({"Api"})
     */
    public function getStatus(): string
    {
        return self::STATUS;
    }
}
