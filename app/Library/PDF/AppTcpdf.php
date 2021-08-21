<?php

namespace App\Library\PDF;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

class AppTcpdf
{
    // 出力ファイル
    public const OUTPUT_FORMAT_CHAR = 'F';
    public const OUTPUT_FILE_DIR = __DIR__.'/../../../storage/app/event/pdf/dist/';
    protected const OUTPUT_FILE_NAME_BASE = 'wrote';

    // PDF
    /** @var string P or L */
    public const PDF_ORIENTATION = 'L';
    public const PDF_UNIT = 'mm';
    public const PDF_SIZE = 'A4';

    /** @var string /resource/fonts/ からのフォントファイルのパス */
    public const FONT_FILE_NAME = 'ipag.ttf';

    /**
     * @var Fpdi
     */
    public Fpdi $fpdi;
    public string $fontFileName;

    /**
     * AppTcpdf constructor.
     * @param  string|null  $sourcePdfFilePath  ソースPDFのファイルパス
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfReaderException
     * @throws PdfTypeException
     */
    public function __construct(?string $sourcePdfFilePath = null)
    {
        $sourcePdfFilePath ??= __DIR__.'/../../../storage/app/event/pdf/template/ticket_by_hand_template.pdf';
        // フォント指定
        $this->fontFileName = resource_path('/fonts/' . self::FONT_FILE_NAME);

        $this->fpdi = new Fpdi(self::PDF_ORIENTATION, self::PDF_UNIT, self::PDF_SIZE);
        $this->fpdi->SetSourceFile($sourcePdfFilePath);
        $this->fpdi->setPrintHeader(false);
        $page = $this->fpdi->importPage(1);
        $this->fpdi->AddPage(self::PDF_ORIENTATION, self::PDF_SIZE, true);
        $this->fpdi->useTemplate($page, 0, 0, 209.9, 297, true);

        $this->fpdi->SetFont($this->getFont());
        $this->fpdi->SetMargins(0, 0);
        $this->fpdi->SetAutoPageBreak(true);// PageBreakTrigger の上書き
    }

    /**
     * 出力文字を設定する
     * @param  string         $_text  文字列
     * @param  int            $_x     X座標
     * @param  int            $_y     Y座標
     * @param  RgbColor|null  $_fontColor
     * @param  int            $_h     高さ
     * @param  int            $fontSize
     */
    public function setText(string $_text, int $_x, int $_y, RgbColor $_fontColor = null, int $_h = 0, int $fontSize = 9): void
    {
        $_fontColor ??= new RgbColor(0, 0, 0);
        $this->fpdi->SetFontSize($fontSize);
        $this->fpdi->SetTextColor($_fontColor->r, $_fontColor->g, $_fontColor->b);
        $this->fpdi->SetXY($_x, $_y);
        $this->fpdi->Write($_h, $_text);
    }

    /**
     * 出力文字を設定する
     * @param  string         $_text  文字列
     * @param  int            $_x     X座標
     * @param  int            $_y     Y座標
     * @param  int            $w
     * @param  RgbColor|null  $_fontColor
     * @param  int            $_h     高さ
     * @param  int            $fontSize
     */
    public function setTextWrap(string $_text, int $_x, int $_y, int $w, RgbColor $_fontColor = null, int $_h = 0, int $fontSize = 9): void
    {
        $_fontColor ??= new RgbColor(0, 0, 0);
        $this->fpdi->SetFontSize($fontSize);
        $this->fpdi->SetTextColor($_fontColor->r, $_fontColor->g, $_fontColor->b);
        $this->fpdi->SetXY($_x, $_y);
        $this->fpdi->MultiCell($w, $_h, $_text);
    }


    /**
     * @param  int|double     $_x
     * @param  int|double     $_y
     * @param  int|double     $r
     * @param  RgbColor|null  $lineColor
     */
    public function setCircle($_x, $_y, $r = 5, RgbColor $lineColor = null): void
    {
        $lineColor ??= new RgbColor(0, 0, 0);
        $this->fpdi->Circle(
            $_x,
            $_y,
            $r,
            $angstr = 0,
            $angend = 360,
            $style = '',
            $line_style = ['color' => $lineColor->forFpdiArray()],
            $fill_color =  [],
            $nc = 2
        );
    }


    public function setCell($x, $y, int $w, int $h, RgbColor $backgroundColor = null): void
    {
        $backgroundColor ??= new RgbColor(255, 255, 255);
        $this->fpdi->SetXY($x, $y);
        [$r, $g,$b] = $backgroundColor->forFpdiArray();
        $this->fpdi->SetFillColor($r, $g, $b);
        $this->fpdi->Cell($w, $h, null, ['LTRB' => ['width' => 0.5, 'color' => [0, 0, 0]]], 1, 'L', 1);
    }

    /**
     * 追加するフォントを取得する
     * @return string|false
     */
    private function getFont()
    {
        return TCPDF_FONTS::addTTFfont($this->fontFileName);
    }

    /**
     * PDFファイル出力
     * @param string $outputFilePath
     */
    public function outputPdfFile(string $outputFilePath): void
    {
        $this->fpdi->Output(
            $outputFilePath,
            self::OUTPUT_FORMAT_CHAR
        );
    }

    public function setGrid($spacing = 5): void
    {
        $fpdi = $this->fpdi;
        $fpdi->SetDrawColor(204, 255, 255);
        $fpdi->SetLineWidth(0.35);
        for ($i = 0; $i < $fpdi->getPageWidth(); $i += $spacing) {
            $fpdi->Line($i, 0, $i, $fpdi->getPageHeight());
        }
        for ($i = 0; $i < $fpdi->getPageHeight(); $i += $spacing) {
            $fpdi->Line(0, $i, $fpdi->getPageWidth(), $i);
        }
        $fpdi->SetDrawColor(154, 200, 255);

        $fpdi->SetTextColor(204, 204, 204);
        $fpdi->SetLineWidth(0.5);
        for ($i = 20; $i < $fpdi->getPageHeight(); $i += 20) {
            $fpdi->SetXY(1, $i - 3);
            $fpdi->Write(4, $i);
            $fpdi->Line(0, $i, $fpdi->getPageWidth(), $i);
        }
        for ($i = 20; $i < (($fpdi->getPageWidth()) - ($fpdi->getMargins()['right']) - 10); $i += 20) {
            $fpdi->SetXY($i - 1, 1);
            $fpdi->Write(4, $i);
            $fpdi->Line($i, 0, $i, $fpdi->getPageHeight());
        }
        $fpdi->SetDrawColor(0, 0, 0);
    }
}
