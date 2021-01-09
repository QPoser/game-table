<?php

declare(strict_types=1);

namespace App\Helper;

final class PaginationHelper
{
    public static function createPaginationArray(?int $total, ?int $limit, ?int $offset): array
    {
        $pagination = [
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'is_last_page' => false,
        ];

        if ($total !== null) {
            if ($limit === null) {
                $pagination['is_last_page'] = true;
            }

            if ($offset !== null) {
                $pagination['is_last_page'] = ($total <= ($limit + $offset));
            }
        }

        return $pagination;
    }
}
