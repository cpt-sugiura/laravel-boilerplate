<?php

namespace App\Console\Commands\ForDevelop;

use DB;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use InfyOm\Generator\Commands\BaseCommand;
use InfyOm\Generator\Common\CommandData;

class DumpInfyomJsonFromTable extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'dump:infyom:schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'データベースを元にinfyom用のJSONを作成する';

    public const DB_TYPE_TO_VALIDATION_MAP = [
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
        'time'       => 'string',
        'float'      => 'numeric',
        'double'     => 'numeric',
        'real'       => 'numeric',
        'decimal'    => 'numeric',
        'numeric'    => 'numeric',
        'year'       => 'integer',
        'longblob'   => 'string',
        'blob'       => 'string',
        'mediumblob' => 'string',
        'tinyblob'   => 'string',
        'binary'     => 'string',
        'varbinary'  => 'string',
        'set'        => 'array',
    ];
    public const SIGNED_INTEGER_BETWEEN_MAP = [
        'tinyint'   => 'between:-128,127',
        'smallint'  => 'between:-32768,32767',
        'mediumint' => 'between:-8388608,8388607',
        'int'       => 'between:-2147483648,2147483647',
        'integer'   => 'between:-2147483648,2147483647',
        'bigint'    => 'between:-9223372036854775808,9223372036854775807',
    ];
    public const UNSIGNED_INTEGER_BETWEEN_MAP = [
        'tinyint'   => 'between:0,255',
        'smallint'  => 'between:0,65535',
        'mediumint' => 'between:0,16777215',
        'int'       => 'between:0,4294967295',
        'integer'   => 'between:0,4294967295',
        'bigint'    => 'between:0,18446744073709551615',
    ];
    /**
     * @var AbstractSchemaManager
     */
    private AbstractSchemaManager $schemaManager;

    /**
     * Execute the console command.
     *
     * @throws Exception
     * @return void
     */
    public function handle(): void
    {
        $this->schemaManager = DB::getDoctrineSchemaManager();
        $this->schemaManager->getDatabasePlatform()
            ->registerDoctrineTypeMapping('geometry', 'string');
        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_API);
        parent::handle();

        $path = config('infyom.laravel_generator.path.schema_files', resource_path('model_schemas/'));

        $fileName = $this->commandData->modelName.'.json';

        if (file_exists($path.$fileName)) {
            $this->warn($path.$fileName.'には既にスキーマファイルが存在しています');

            return;
        }

        $columns = $this->schemaManager->listTableColumns($this->commandData->dynamicVars['$TABLE_NAME$']);

        $this->setValidationRules($columns);
        $this->performPostActions();
    }

    /**
     * @param array $columns
     */
    private function setValidationRules(array $columns): void
    {
        foreach ($this->commandData->fields as $index => $field) {
            if (in_array($field->name, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }
            $column         = $columns[$field->name];
            $columnTypeName = $column->getType()->getName();
            /** @var array $rules */
            $rules   = $column->getNotnull() ? [] : ['nullable'];
            $rules[] = self::DB_TYPE_TO_VALIDATION_MAP[$columnTypeName];
            if (array_key_exists($columnTypeName, self::UNSIGNED_INTEGER_BETWEEN_MAP)) {
                $rules[] = $column->getUnsigned()
                    ? self::UNSIGNED_INTEGER_BETWEEN_MAP[$column->getType()->getName()]
                    : self::SIGNED_INTEGER_BETWEEN_MAP[$column->getType()->getName()];
            }
            if ($column->getLength()) {
                $rules[] = 'max:'.$column->getLength();
            }

            $this->commandData->fields[$index]->validations = implode('|', $rules);
        }
    }
}
