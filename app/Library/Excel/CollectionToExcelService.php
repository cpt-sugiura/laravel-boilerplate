<?php

namespace App\Library\Excel;

use Illuminate\Support\Collection;
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

    /**
     * Excelファイルとしてダウンロード
     * @param  string       $fileName
     * @param  string|null  $writerType
     * @param  array        $responseHeaders
     * @return BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function download(string $fileName, ?string $writerType = null, array $responseHeaders = []): BinaryFileResponse
    {
        return Excel::download($this, $fileName, $writerType, $responseHeaders);
    }

    /**
     * Excelファイルとして保存
     * @param  string       $filePath
     * @param  string|null  $disk
     * @param  string|null  $writerType
     * @param  array        $diskOptions
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function store(string $filePath, string $disk = null, string $writerType = null, array $diskOptions = []): bool
    {
        return Excel::store($this, $filePath, $disk, $writerType, $diskOptions);
    }
}
