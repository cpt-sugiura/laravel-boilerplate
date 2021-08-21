<?php

declare(strict_types=1);

namespace App\Database\Schema\Grammars;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\MySqlGrammar as BaseMySqlGrammar;
use Illuminate\Support\Fluent;

class MySqlGrammar extends BaseMySqlGrammar
{
    public function __construct()
    {
        if (false !== $position = array_search('VirtualAs', $this->modifiers, true)) {
            array_splice($this->modifiers, $position, 0, ['VirtualAsNotNull']);
        }
        if (false !== $position = array_search('StoredAs', $this->modifiers, true)) {
            array_splice($this->modifiers, $position, 0, ['StoredAsNotNull']);
        }
    }

    /**
     * Get the SQL for a generated virtual column modifier with NOT NULL constraint.
     *
     * @param  Blueprint   $blueprint
     * @param  Fluent      $column
     * @return string|null
     */
    protected function modifyVirtualAsNotNull(Blueprint $blueprint, Fluent $column): ?string
    {
        return $column->virtualAsNotNull !== null ? " as ({$column->virtualAsNotNull}) virtual" : null;
    }

    /**
     * Get the SQL for a generated stored column modifier with NOT NULL constraint.
     *
     * @param  Blueprint   $blueprint
     * @param  Fluent      $column
     * @return string|null
     */
    protected function modifyStoredAsNotNull(Blueprint $blueprint, Fluent $column): ?string
    {
        return $column->storedAsNotNull !== null ? " as ({$column->storedAsNotNull}) stored" : null;
    }

    /**
     * FULLTEXT インデックス作成コマンドの生成
     *
     * @param  Blueprint   $blueprint
     * @param  Fluent      $command
     * @return string|null
     */
    public function compileFulltextIndex(Blueprint $blueprint, Fluent $command): ?string
    {
        return sprintf(
            'alter table %s add %s %s(%s)%s',
            $this->wrapTable($blueprint),
            'fulltext index',
            $this->wrap($command->index),
            $this->columnize($command->columns),
            $command->algorithm ? ' with parser '.$command->algorithm : '',
        );
    }

    /**
     * FULLTEXT インデックス削除コマンドの生成
     *
     * @param  Blueprint $blueprint
     * @param  Fluent    $command
     * @return string
     */
    public function compileDropFulltextIndex(Blueprint $blueprint, Fluent $command): string
    {
        return $this->compileDropIndex($blueprint, $command);
    }
}
