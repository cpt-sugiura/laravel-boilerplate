<?php

namespace App\Library\DumpSourceCode;

use Doctrine\DBAL\Types\Type;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class CastDbTypeMap
{
    public const DB_TYPE_TO_LARAVEL_CAST_MAP = [
        'boolean'    => 'boolean',
        'tinyint'    => 'integer',
        'smallint'   => 'integer',
        'mediumint'  => 'integer',
        'int'        => 'integer',
        'integer'    => 'integer',
        'bigint'     => 'integer',
        'tinytext'   => 'string',
        'mediumtext' => 'string',
        'longtext'   => 'string',
        'text'       => 'string',
        'varchar'    => 'string',
        'string'     => 'string',
        'char'       => 'string',
        'date'       => 'date',
        'datetime'   => 'date',
        'timestamp'  => 'date',
        'time'       => 'date',
        'float'      => 'float',
        'double'     => 'float',
        'real'       => 'float',
        'decimal'    => 'float',
        'numeric'    => 'float',
        'year'       => 'integer',
        'longblob'   => 'string',
        'blob'       => 'string',
        'mediumblob' => 'string',
        'tinyblob'   => 'string',
        'binary'     => 'string',
        'varbinary'  => 'string',
        'set'        => 'array',
        'geometry'   => null,
        'point'      => Point::class,
        'polygon'    => Polygon::class,
    ];
    public const LARAVEL_CAST_TO_TYPESCRIPT_TYPE_MAP = [
        'boolean'      => 'boolean',
        'integer'      => 'number',
        'string'       => 'string',
        'date'         => 'string',
        'float'        => 'number',
        'array'        => 'string[]',
        null           => 'null',
        Point::class   => 'object',
        Polygon::class => 'object',
    ];
    public const LARAVEL_CAST_TO_TYPESCRIPT_DEFAULT_VALUE_STR = [
        'boolean'      => 'false',
        'integer'      => '0',
        'string'       => "''",
        'date'         => "''",
        'float'        => '0',
        'array'        => '[]',
        null           => 'null',
        Point::class   => '{}',
        Polygon::class => '{}',
    ];

    public static function dbTypeToTypeScriptType(string|Type $type): string
    {
        if ($type instanceof Type) {
            $type = $type->getName();
        }

        return self::LARAVEL_CAST_TO_TYPESCRIPT_TYPE_MAP[self::DB_TYPE_TO_LARAVEL_CAST_MAP[$type] ?? null] ?? '';
    }

    public static function dbTypeToTypeScriptDefaultVal(string|Type $type): string
    {
        if ($type instanceof Type) {
            $type = $type->getName();
        }

        return self::LARAVEL_CAST_TO_TYPESCRIPT_DEFAULT_VALUE_STR[self::DB_TYPE_TO_LARAVEL_CAST_MAP[$type] ?? null] ?? '';
    }
}
