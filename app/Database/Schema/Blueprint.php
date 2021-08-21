<?php

namespace App\Database\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBluePrint;
use Illuminate\Support\Fluent;

class Blueprint extends BaseBluePrint
{
    /**
     * fulltext index 生成の指定
     *
     * @param  string|array $columns
     * @param  string|null  $name
     * @param  string|null  $algorithm
     * @return Fluent
     */
    public function fulltextIndex($columns, ?string $name = null, ?string $algorithm = null): Fluent
    {
        return $this->indexCommand('fulltextIndex', $columns, $name, $algorithm);
    }

    /**
     * fulltext index 削除の指定
     *
     * @param  string|array $index
     * @return Fluent
     */
    public function dropFulltextIndex($index): Fluent
    {
        return $this->dropIndexCommand('dropFulltextIndex', 'fulltextIndex', $index);
    }

    /**
     * fulltext index with parser ngram 生成の指定
     *
     * @param  string|array $columns
     * @param  string|null  $name
     * @return Fluent
     */
    public function ngramIndex($columns, ?string $name = null): Fluent
    {
        return $this->indexCommand('fulltextIndex', $columns, $name, 'ngram');
    }

    /**
     * fulltext index 削除の指定
     *
     * @param  string|array $index
     * @return Fluent
     */
    public function dropNgramIndex($index): Fluent
    {
        return $this->dropIndexCommand('dropFulltextIndex', 'fulltextIndex', $index);
    }
}
