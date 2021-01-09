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
        $this->limit = $request->query->has('limit') ? (int) $request->query->get('limit') : null;
        $this->offset = $request->query->has('offset') ? (int) $request->query->get('offset') : null;
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
