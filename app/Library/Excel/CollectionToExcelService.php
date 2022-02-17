<?php

namespace App\Library\Excel;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Illuminate\Support\Collection と Excelのなんやかんやをつなぐクラス
 * Class CollectionToExcel
 * @package App\Library\Excel
 */
class CollectionToExcelService implements FromCollection
{
    use Exportable;

    protected Collection $collection;

    /**
     * constructor.
     * @param Collection $collection
     * @param array|null $header
     */
    public function __construct(Collection $collection, ?array $header = null)
    {
        if (is_array($header)) {
            $this->collection = collect([$header])->merge($collection);
        }
    }

    /**
     * Maatwebsite\ExcelにCollectionをわたすために必要なインターフェース
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->collection;
    }
}
