<?php

namespace App\Console\Commands\ForDevelop\ModelMaker;

use App\Console\BaseCommand;
use App\Models\Eloquents\BaseEloquent;

use function class_basename;
use function config;

class AppendRuleAttributesToModel extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:append-model-rule-attributes {classNamePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'モデル中の public static function rules() メソッドの返り値のキーに対応する attributes メソッドを追加';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        /** @var BaseEloquent $modelNamePath */
        $modelNamePath = $this->argument('classNamePath');
        $rules         = $modelNamePath::rules();
        $ruleKeys      = array_keys($rules);

        $nameToCommentMap = $this->getCommentMapFromTable(new $modelNamePath());

        $attributeMap = [];
        foreach ($ruleKeys as $ruleKey) {
            $attributeMap[$ruleKey] = $nameToCommentMap[$ruleKey] ?? '';
        }

        $modelFilePath = config('infyom.laravel_generator.path.model').DIRECTORY_SEPARATOR.class_basename($modelNamePath).'.php';

        $content         = file_get_contents($modelFilePath);
        $replacedContent = $this->replacer($content, $attributeMap);

        file_put_contents($modelFilePath, $replacedContent);
        return static::SUCCESS;
    }

    /**
     * @param  BaseEloquent $model
     * @return array        {columnName => comment}[]
     */
    protected function getCommentMapFromTable(BaseEloquent $model): ?array
    {
        $table  = $model->getConnection()->getTablePrefix().$model->getTable();
        $schema = $model->getConnection()->getDoctrineSchemaManager();

        $database = null;
        if (strpos($table, '.')) {
            [$database, $table] = explode('.', $table);
        }

        $columns = $schema->listTableColumns($table, $database);

        $nameToCommentMap = [];
        foreach ($columns as $column) {
            $nameToCommentMap[$column->getName()] = $column->getComment();
        }

        return $nameToCommentMap;
    }

    protected function replacer(string $content, array $attributeMap): string
    {
        $attributeMapStr = '';
        foreach ($attributeMap as $key => $attribute) {
            $attributeMapStr .= "              '{$key}' => '{$attribute}',\n";
        }
        $appendFunctionStr = <<<EOF
    public static function ruleAttributes(): array
    {
        return [
{$attributeMapStr}
        ];
    }
EOF;

        return preg_replace('/}\s*\z/', $appendFunctionStr."\n}\n", $content);
    }
}
