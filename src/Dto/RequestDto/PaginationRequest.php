<?php

declare(strict_types=1);

namespace App\Dto\RequestDto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class PaginationRequest implements RequestDTOInterface
{
    /**
     * @Assert\PositiveOrZero
     */
    private ?int $limit;

    /**
     * @Assert\PositiveOrZero
     */
    private ?int $offset;

    public function __construct(Request $request)
    {
        $this->limit = $request->query->get('limit');
        $this->offset = $request->query->get('offset');
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }
}
