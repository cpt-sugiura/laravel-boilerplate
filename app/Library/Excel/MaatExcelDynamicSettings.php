<?php

namespace App\Library\Excel;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

/**
 * 与えられたファイルから動的に設定を決定する Maatwebsite\Excel 用のクラス
 */
class MaatExcelDynamicSettings implements WithCustomCsvSettings
{
    /** @var UploadedFile|string アップロードファイルインスタンスかファイルパス */
    private UploadedFile | string $file;

    /**
     * @param UploadedFile|string $file アップロードファイルインスタンスかファイルパス
     */
    public function __construct(UploadedFile | string $file)
    {
        $this->file = $file;
    }

    public function getCsvSettings(): array
    {
        // 文字コードを推定するためにファイルの最初の 1 行目を取得
        if (is_string($this->file)) {
            // $this->file がファイルパスならそのまま file 関数に渡して一行目を取得
            $firstLine = file($this->file)[0] ?? '';
        } else {
            // $this->file ファイルインスタンスならフルパスを得てから file 関数に渡して一行目を取得
            $firstLine = file($this->file->getPathname())[0] ?? '';
        }

        return [
            // 文字コードを推定
            'input_encoding' => mb_detect_encoding(
                $firstLine,
                // 文字コード候補を明示
                // 未定義にすると php.ini 等で設定された mbstring.detect_order に従って文字コード候補が決定されます
                [
                    'ASCII',
                    'ISO-2022-JP',
                    'UTF-8',
                    'EUC-JP',
                    'SJIS',
                    'SJIS-win',
                ]
            )
        ];
    }
}
