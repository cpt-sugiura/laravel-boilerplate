<?php

namespace App\Console\Commands\ForDevelop\MakeTestData;

/**
 * SHOW COLUMNS FROM {TABLE}; で得られた結果の行のモデル
 */
class ShowColumnRecordModel
{
    //   $col ~ object(stdClass)#910 (6) {
    //    ["Field"]=>
    //    string(2) "id"
    //    ["Type"]=>
    //    string(16) "int(10) unsigned"
    //    ["Null"]=>
    //    string(2) "NO"
    //    ["Key"]=>
    //    string(3) "PRI"
    //    ["Default"]=>
    //    NULL
    //    ["Extra"]=>
    //    string(14) "auto_increment"
    //  }
    private function __construct(
        public string $field,
        public string $type,
        public bool $nullable,
        public string $key,
        public string|null|int|float $default,
        public string $extra,
    ) {
    }

    /**
     * @param  object $record
     * @return static
     */
    public static function createFromStdObject(object $record): self
    {
        return new self(
            $record->Field,
            $record->Type,
            $record->Null === 'YES',
            $record->Key,
            $record->Default,
            $record->Extra,
        );
    }

    public function getEnumOptions(): array
    {
        if (! str_starts_with($this->type, 'enum')) {
            return [];
        }
        $optionsStr = preg_replace('/^enum\(|\)$/', '', $this->type);
        // todo nits これではカンマを含まない文字列型限定でしか使えないので拡張した方がよさげ。
        $optionsStrList = explode(',', $optionsStr);

        return array_map(static fn (string $op) => trim($op, '\'"'), $optionsStrList);
    }

    public function isPrimaryKey(): bool
    {
        return str_contains($this->key, 'PRI');
    }

    public function isAutoIncrement(): bool
    {
        return str_contains($this->extra, 'auto_increment');
    }
}
