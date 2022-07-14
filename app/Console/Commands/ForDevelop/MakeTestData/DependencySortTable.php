<?php

namespace App\Console\Commands\ForDevelop\MakeTestData;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use RuntimeException;

class DependencySortTable
{
    private const MAX_LOOP = 1000;

    /**
     * @param  Table[]  $tables
     * @param  string[] $resolvedTableNames
     * @return Table[]
     */
    public function __invoke(array $tables, array $resolvedTableNames = []): array
    {
        /** @var Table[] $ret */
        $ret = [];
        $i   = 0; // 無限ループ防止
        while (! empty($tables) && $i < self::MAX_LOOP) {
            $allocatedTableNames = [...$resolvedTableNames, ...array_map(static fn (Table $t) => $t->getName(), $ret)];

            foreach ($tables as $k => $tab) {
                if (in_array($tab->getName(), $resolvedTableNames, true)) {
                    $ret[] = $tab;
                    unset($tables[$k]);
                    continue;
                }
                // テーブルの外部キー制約の内、返り値に含め済みのテーブルを参照している外部キー制約を除去
                $fkList           = $tab->getForeignKeys();
                $notResolveFkList = array_filter(
                    $fkList,
                    static fn (ForeignKeyConstraint $fk) => ! in_array($fk->getForeignTableName(), $allocatedTableNames, true)
                );
                // もし返り値に含め済みのテーブルだけで依存性を解決できるならば、返り値にテーブルを追加
                if (empty($notResolveFkList)) {
                    $ret[] = $tab;
                    unset($tables[$k]);
                }
            }
            ++$i;
        }
        if ($i >= self::MAX_LOOP) {
            throw new RuntimeException('ループ数が多すぎました。');
        }

        return $ret;
    }
}
