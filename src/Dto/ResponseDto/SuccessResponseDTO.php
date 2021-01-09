<?php

declare(strict_types=1);

namespace App\Dto\ResponseDto;

use Symfony\Component\Serializer\Annotation\Groups;

class SuccessResponseDTO extends ResponseDTO
{
    private const STATUS = 'success';

    /**
     * @var mixed
     * @Groups({"Api"})
     */
    private $data;

    /**
     * @Groups({"Api"})
     */
    private array $partials;

    public function __construct($data, array $partials = [])
    {
        $this->data = $data;
        $this->partials = $partials;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPartials(): array
    {
        return $this->partials;
    }

    /**
     * @Groups({"Api"})
     */
    public function getStatus(): string
    {
        return self::STATUS;
    }
}