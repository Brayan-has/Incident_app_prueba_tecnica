<?php

namespace App\Concerns\Traits;

trait PaginationTrait
{
    public function paginate($items)
    {
        $pagination = [
            'data' => collect($items->items())->toArray(), 
            'current_page' => $items->currentPage(), 
            'next_page_url' => $items->nextPageUrl(),
            'prev_page_url' => $items->previousPageUrl(),
            'total' => $items->total(), 
            'per_page' => $items->perPage(), 
        ];

        return $pagination;
    }
}
