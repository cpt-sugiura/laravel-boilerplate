<?php

namespace App\Library\DumpSourceCode;

use App\Models\Eloquents\BaseEloquent;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Error;
use Illuminate\Database\Eloquent\Model;
use Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ColumnForRender
{
    public function __construct(
        public BaseEloquent $model,
        public Column $column
    ) {
    }

    /**
     * @return AbstractSchemaManager
     */
    protected function getDoctrineSchemaManager(): AbstractSchemaManager
    {
        return $this->model->getConnection()->getDoctrineSchemaManager();
    }

    public function visible(): bool
    {
        $visibleColumnNames    = array_diff(
            array_keys($this->model->getCasts(true)),
            $this->model->getHidden()
        );

        return in_array($this->column->getName(), $visibleColumnNames, true);
    }

    public function searchable(): bool
    {
        $searchableColumnNames = array_diff(
            array_keys($this->model->getCasts(true)),
            ['deleted_at', ...$this->model->getHidden()]
        );

        return in_array($this->column->getName(), $searchableColumnNames, true);
    }

    public function editable(): bool
    {
        $editableColumnNames   = array_diff(
            array_keys($this->model->getCasts(true)),
            ['deleted_at', ...$this->model->getHidden(), ...$this->model->getGuarded()]
        );

        return in_array($this->column->getName(), $editableColumnNames, true);
    }

    public function getFullNameInDB(): string
    {
        return $this->model->getTable().'.'.$this->column->getName();
    }

    public function getNameAsRequestKey(): string
    {
        return Str::camel($this->column->getName());
    }

    public function getTypeScriptType(): string
    {
        return CastDbTypeMap::dbTypeToTypeScriptType($this->column->getType());
    }

    public function getTypeScriptDefaultValue(): string
    {
        return CastDbTypeMap::dbTypeToTypeScriptDefaultVal($this->column->getType());
    }

    public function getLabel(): string
    {
        return explode('。', $this->column->getComment())[0];
    }

    public function getReactYupValidation(): string
    {
        $colName = $this->column->getName();
        if (preg_match('/(tel|phone_number)$/', $colName)) {
            $method = 'phoneNumber()';
        } elseif (preg_match('/mail$|mail_?address$/', $colName)) {
            $method = 'email()';
        } elseif (preg_match('/zip$|zip_?code$/', $colName)) {
            $method = 'zip()';
        } elseif (CastDbTypeMap::dbTypeToTypeScriptType($this->column->getType()) === 'number') {
            $method = 'numeric()';
        } elseif (CastDbTypeMap::dbTypeToTypeScriptType($this->column->getType()) === 'boolean') {
            $method = '';
        } else {
            $method = 'max('.$this->column->getLength().')';
        }
        $required = $this->column->getNotnull() ? 'required()' : 'nullable()';

        $method = $method ? ('.'.$method) : $method;

        return 'yup.string().'.$required.$method.".label('".$this->getLabel()."')";
    }

    /**
     * @throws Exception
     * @return ForeignKeyConstraint[]
     */
    public function foreignKeys(): array
    {
        $fkList = $this->getDoctrineSchemaManager()->listTableForeignKeys($this->model->getTable());

        return array_filter($fkList, fn (ForeignKeyConstraint $fk) => $fk->getLocalColumns()[0] === $this->column->getName());
    }

    /**
     *@throws Exception
     * @return ColumnForRender[]
     */
    public function foreignKeyColumns(): array
    {
        $ret = [];
        foreach ($this->foreignKeys() as $fk) {
            // 外部キーでつながっているテーブル
            $fkModel = null;
            foreach ($this->getEloquentClassNames() as $modelName) {
                /** @var BaseEloquent $modelName */
                if ($fk->getForeignTableName() === (new $modelName())->getTable()) {
                    $fkModel = new $modelName();
                    break;
                }
            }
            if ($fkModel === null) {
                continue;
            }
            // 外部キーでつながっているテーブルの内、検索に使いたいカラム
            foreach ($this->getDoctrineSchemaManager()->listTableColumns($fk->getForeignTableName()) as $col) {
                if (preg_match('/_?name$/', $col->getName())) {
                    $ret[] = new self($fkModel, $col);
                }
            }
        }

        return $ret;
    }

    /**
     * @return string[]
     */
    private function getEloquentClassNames(): array
    {
        // プロジェクトの Eloquents を置いてあるディレクトリ以下の PHP ファイルを探索
        // この探索は再帰的に行われる
        // @see https://symfony.com/doc/current/components/finder.html
        $files = (new Finder())->in(app_path('Models/Eloquents'))
            ->files()
            ->name('*.php');
        // 見つかったファイルパスを元にクラス名一覧を作る
        $classNames = [];
        /** @var SplFileInfo $fileInfo */
        foreach ($files->getIterator() as $fileInfo) {
            $classNames[] = str_replace(
            // ファイルパスを名前空間に入れ替え、拡張子を除去
                [app_path('Models/Eloquents'), '/', '.php'],
                ['App\\Models\\Eloquents', '\\', ''],
                $fileInfo->getRealPath()
            );
        }
        // クラス名の中から Eloquent を継承したクラスのみを抜き出す
        $eloquentClassNames = [];
        foreach ($classNames as $className) {
            try {
                // クラス名からインスタンスを作成。 Eloquent を継承しているか確認
                $instance = new $className();
                if ($instance instanceof Model) {
                    $eloquentClassNames[] = $className;
                }
            } /* @noinspection PhpUnusedLocalVariableInspection */ catch (Error $exception) {
                // インスタンス化できない対象について new を行った際のエラーを握りつぶす
                // abstract class や trait が引っかかりやすい
            }
        }

        return $eloquentClassNames;
    }

    public function getCastType()
    {
        return $this->model->getCasts()[$this->column->getName()];
    }
}
