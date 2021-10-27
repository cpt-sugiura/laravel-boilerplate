<?php

namespace App\Http\Presenters;

use Illuminate\Contracts\Pagination\Paginator;

/**
 * ページネータをJavaScriptライクなキャメルケースベースに変換するプレゼンター
 * Class SimplePaginatorPresenter
 * @package App\Http\Presenters
 */
class SimplePaginatorPresenter extends BasePresenter
{
    /**
     * @var Paginator
     */
    public Paginator $paginator;
    public ?int $total;

    /**
     * PaginatorPresenter constructor.
     * @param Paginator $paginator
     * @param int|null  $total
     */
    public function __construct(Paginator $paginator, ?int $total = null)
    {
        $this->paginator = $paginator;
        $this->total     = $total;
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
            'total'       => $this->total,
            'perPage'     => +$this->paginator->perPage(),
            'currentPage' => +$this->paginator->currentPage(),
            'lastPage'    => max((int) ceil($this->total / $this->paginator->perPage()), 1),
            'from'        => +$this->paginator->firstItem(),
            'to'          => +$this->paginator->lastItem(),
        ];
    }
}
