<?php

namespace App\Http\Presenters;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ページネータをJavaScriptライクなキャメルケースベースに変換するプレゼンター
 * Class Paginator
 * @package App\Http\Presenters
 */
class PaginatorPresenter extends BasePresenter
{
    /**
     * @var LengthAwarePaginator
     */
    public LengthAwarePaginator $paginator;

    /**
     * PaginatorPresenter constructor.
     * @param LengthAwarePaginator $paginator
     */
    public function __construct(LengthAwarePaginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'data'                => array_map(
                static fn ($item) => is_object($item) && method_exists($item, 'toArray') ? $item->toArray() : $item,
                $this->paginator->items()
            ),
            'total'       => +$this->paginator->total(),
            'perPage'     => +$this->paginator->perPage(),
            'currentPage' => +$this->paginator->currentPage(),
            'lastPage'    => +$this->paginator->lastPage(),
            'from'        => +$this->paginator->firstItem(),
            'to'          => +$this->paginator->lastItem(),
        ];
    }
}
