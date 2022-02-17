<?php

namespace App\Library\Excel\Model;


use Closure;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Excel シートモデル
 */
abstract class ExcelSheet implements WithTitle, FromArray, WithEvents
{
    /**
     * データ本体を格納する。
     * 外から好き勝手操作することが多いのでいっそ public 化。
     * @var array
     */
    public array $rows = [];

    /**
     * @param  string  $sheetName  シート名
     */
    protected string $sheetName;

    /**
     * @param  string  $sheetName  シート名
     */
    public function __construct(string $sheetName)
    {
        $this->sheetName = $sheetName;
    }

    /**
     * シート名。\Maatwebsite\Excel\Concerns\WithTitle の指定
     * @return string
     */
    public function title(): string
    {
        return $this->sheetName;
    }

    /**
     * データ本体。\Maatwebsite\Excel\Concerns\FromArray の指定
     * @return array
     */
    public function array(): array
    {
        return $this->rows;
    }

    /**
     * Maatwebsite\Excel の各処理で発火するイベントを登録するメソッド
     *
     * \Maatwebsite\Excel\Concerns\WithEvents の指定
     *
     * @see https://docs.laravel-excel.com/3.1/exports/extending.html#customize
     * @return Closure[]
     */
    public function registerEvents(): array
    {
        return [
            // シートについての処理の最後に発火するイベント
            // @see https://docs.laravel-excel.com/3.1/architecture/#processing-the-sheets-2
            AfterSheet::class => fn(AfterSheet $event) => $this->onAfterSheet($event),
        ];
    }

    /**
     * @param  AfterSheet  $event
     * @return void
     * @throws Exception
     */
    protected function onAfterSheet(AfterSheet $event): void
    {
        // シートについて色々細かく操作できる \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet インスタンスを
        // Laravel 用にラッピングされた \Maatwebsite\Excel\Sheet の中から呼び出す
        $sheet = $event->sheet->getDelegate();
        // 対象のセルらを折り返しありにする。
        $useRowLastIndex = count($this->rows) + $this->getBodyStartIndexInExcel();

        $sheet->getStyle("A1:D" . $useRowLastIndex)
            ->getAlignment()->setWrapText(true);
        // 上揃え
        $sheet->getStyle("A1:D" . $useRowLastIndex)
            ->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

        // 縞々
        for($i = 2; $i <= $useRowLastIndex; $i += 2) {
            $sheet->getStyle("A$i:D$i")
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('F0F0F0');
        }

        $sheet->getSheetView()->setZoomScale(85);

        // Excel を開いた時のカーソルを左上にあわせる
        // 枠線など色々操作するとカーソルが飛ぶ
        $sheet->setSelectedCells("A1:A1");
    }


    /**
     * 行の高さをデータに合わせて調整
     * @param  Worksheet  $sheet
     * @return void
     */
    protected function adjustRowHeight(Worksheet $sheet): void
    {
        // 行のデータの最大行数を得る
        $getMaxLineCount = static fn(array $rowVal) => max(
            PHP_INT_MIN,// 番人。ないと max が PHP Fatal Error を起こしうる
            ...array_map(
            // 改行コードの数と最初の行である 1 を足して行数を計算
                static fn($cellVal) => substr_count($cellVal, "\n") + 1,
                $rowVal
            )
        );
        // 一文字の高さ
        $charHeight = 14.4;
        foreach($this->rows as $rowNum => $rowVal) {
            // ↑の $getMaxLineCount で得られる行数を Excel 上の高さに変換してセット
            // +2 はヘッダ行と Excel のインデックスが 1 始まりの二つを考慮した +2
            $sheet->getRowDimension($rowNum + $this->getBodyStartIndexInExcel())
                ->setRowHeight($getMaxLineCount($rowVal) * $charHeight);
        }
    }

    /**
     * ヘッダ行と Excel のインデックスが 1 始まりの二つを考慮した本文の開始インデックス IN Excel
     * ヘッダ行ありなら A2 が本文の左上とかそんな感じ
     * @return int
     */
    protected function getBodyStartIndexInExcel(): int
    {
        return 1 + ($this instanceof WithHeadings ? 1 : 0);
    }
}
