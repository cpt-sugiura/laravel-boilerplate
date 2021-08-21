<?php

namespace App\Database\Schema;

use Closure;
use Illuminate\Database\Schema\MySqlBuilder as BaseMySqlBuilder;

class MySqlBuilder extends BaseMySqlBuilder
{
    protected function createBlueprint($table, Closure $callback = null)
    {
        $prefix = $this->connection->getConfig('prefix_indexes')
            ? $this->connection->getConfig('prefix')
            : '';

        if (isset($this->resolver)) {
            return call_user_func($this->resolver, $table, $callback, $prefix);
        }

        return new Blueprint($table, $callback, $prefix);
    }

    public function blueprintResolver(Closure $resolver)
    {
        $this->resolver = static function ($table, $callback) {
            return new Blueprint($table, $callback);
        };
    }
}
