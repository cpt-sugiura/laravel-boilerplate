<?php

namespace App\Library\SQLFormatter;

use Illuminate\Support\Str;
use SqlFormatter as BaseSqlFormatter;

class SqlFormatter
{
    /**
     * @param  string[] $sqls
     * @return string
     */
    public static function concatFormattedSqls(array $sqls): string
    {
        $results = [];
        foreach ($sqls as $offset => $sql) {
            $results[] = implode("\n", [
                "Logged SQL ($offset)",
                '',
                static::format($sql),
            ]);
        }

        return implode("\n------------\n", $results);
    }

    /**
     * @param  string $sql
     * @return string
     */
    public static function format(string $sql): string
    {
        return BaseSqlFormatter::format($sql, false);
    }

    /**
     * @param  string $sql
     * @param  array  $bindings
     * @return string
     */
    public static function replacePlaceholders(string $sql, array $bindings): string
    {
        return Str::replaceArray(
            '?',
            array_map(static fn ($v) => static::replacePlaceholderValue($v), $bindings),
            $sql
        );
    }

    /**
     * @param  mixed  $value
     * @return string
     */
    protected static function replacePlaceholderValue(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        return sprintf("'%s'", addcslashes((string) $value, "\\'"));
    }

    /**
     * @param  array  $sql {query:string; bindings:Array<string|number|bool>; time: number}
     * @return string
     */
    public static function formatWithBind(array $sql): string
    {
        $bound = self::replacePlaceholders($sql['query'], $sql['bindings']);

        return self::format($bound);
    }

    /**
     * @param  array  $sqls \DB::getQueryLogメソッドで取得したクエリログ
     * @return string
     */
    public static function formatWithBindArr(array $sqls): string
    {
        $log = array_map(
            static fn (array $entry) => self::replacePlaceholders($entry['query'], $entry['bindings']),
            $sqls,
        );

        return self::concatFormattedSqls($log);
    }

    /**
     * @param  array  $sqls \DB::getQueryLogメソッドで取得したクエリログ
     * @return string
     */
    public static function formatFromDBGetQueryLog(array $sqls): string
    {
        return self::formatWithBindArr($sqls);
    }
}
