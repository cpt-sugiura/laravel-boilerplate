<?php

namespace App\Library\Zip;

use ArrayAccess;
use ZipArchive;

class Zip
{
    /**
     * @param string            $zipFilePath
     * @param iterable          $filePaths
     * @param array|ArrayAccess $fileNames
     * @param string|null       $password
     */
    public function createFlatFilesZip(string $zipFilePath, iterable $filePaths, array | ArrayAccess $fileNames = [], ?string $password = null): void
    {
        $zip = new ZipArchive();
        ! empty($password) && $zip->setPassword($password);
        $zip->open(
            $zipFilePath,
            ZipArchive::CREATE | ZipArchive::OVERWRITE
        );
        $i = 0;
        foreach ($filePaths as $index => $path) {
            $zip->addFile($path, mb_convert_encoding($fileNames[$index] ?? basename($path), 'CP932', 'UTF-8'));
            ++$i;
        }
        if ($i === 0) {
            throw new \RuntimeException('ZIP化する対象のファイルが存在しません');
        }
        $zip->close();
    }

    /**
     * とあるディレクトリ以下をコピーしたZIPファイルを作る
     * @param string      $zipFilePath ZIPファイル出力先古パス
     * @param string      $src         ZIPファイルにしたいディレクトリのパス
     * @param string|null $password    ZIPファイルに設定するパスワード
     */
    public function dir2zip(string $zipFilePath, string $src, ?string $password = null): void
    {
        $zip = new ZipArchive();
        $zip->open($zipFilePath, ZIPARCHIVE::CREATE);
        ! empty($password) && $zip->setPassword($password);
        $this->zipAddRecursive($zip, $src, '');
        $zip->close();
    }

    /**
     * $src 以下のファイルをディレクトリ構造に従って再帰的に ZIP に登録
     * @param ZipArchive $zip
     * @param string     $src
     * @param string     $parentPath
     */
    protected function zipAddRecursive(ZipArchive $zip, string $src, string $parentPath = ''): void
    {
        $dh = opendir($src);
        while (($entry = readdir($dh)) !== false) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $localPath = $parentPath.$entry;
            $fullPath  = $src.'/'.$entry;
            if (is_file($fullPath)) {
                if (! $zip->addFile($fullPath, $localPath)) {
                    echo sprintf('Add file failed: %s', $localPath);
                }
                if (! $zip->setEncryptionName($localPath, ZipArchive::EM_AES_256)) {
                    echo sprintf('Set encryption failed: %s', $localPath);
                }
            } elseif (is_dir($fullPath)) {
                $zip->addEmptyDir($localPath);
                $this->zipAddRecursive($zip, $fullPath, $localPath.'/');
            }
        }
        closedir($dh);
    }
}
