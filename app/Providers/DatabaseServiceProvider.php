<?php

namespace App\Providers;

use App\Database\MySqlConnection;
use DB;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider;
use Log;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function boot(): void
    {
        Connection::resolverFor('mysql', function (...$parameters) {
            return new MySqlConnection(...$parameters);
        });
        DB::getDoctrineSchemaManager()->getDatabasePlatform()
                ->registerDoctrineTypeMapping('enum', 'string');
        if ($this->isSeedingProcess()) {
            // データベース初期化時はクエリログを書き込まない
            return;
        }
        DB::connection()->enableQueryLog();
        DB::connection()->listen(static function (QueryExecuted $query) {
            if ($query->time < 1000) {
                Log::channel('query_log')->debug($query->sql, [
                    'params'  => $query->bindings,
                    'time_ms' => $query->time,
                    'slow'    => false,
                ]);
            } else {
                Log::channel('query_log')->warning($query->sql, [
                    'params'  => $query->bindings,
                    'time_ms' => $query->time,
                    'slow'    => true,
                ]);
            }
        });
    }

    /**
     * 今走っている PHP プロセスがデータベース初期化処理のプロセスか否か
     * @return bool
     */
    private function isSeedingProcess(): bool
    {
        return isset($_SERVER['argv']) && (in_array('seed', $_SERVER['argv'], true) || in_array('--seed', $_SERVER['argv'], true));
    }
}
