<?php

namespace App\Library\Rules;

use App\Models\Eloquents\BaseEloquent;
use Illuminate\Contracts\Validation\Rule;

/**
 * 論理削除されていないレコードの範囲のみでのユニークルール
 * Class UniqueInNotSoftDeleted
 * @package App\Rules
 */
class UniqueInNotSoftDeleted implements Rule
{
    /** @var BaseEloquent unique ルールで参照するテーブルのインスタンス */
    protected BaseEloquent $eloquentInstance;
    /** @var string unique ルールを適用するカラム */
    protected string $column;
    /** @var string|int|null unique ルールから除外するレコードのID */
    protected $excludeId;

    /**
     * UniqueInNotSoftDeleted constructor.
     * @param BaseEloquent    $eloquentInstance unique ルールで参照するテーブルのインスタンス
     * @param string          $column           unique ルールを適用するカラム
     * @param string|int|null $excludeId        unique ルールから除外するレコードのID
     */
    public function __construct(BaseEloquent $eloquentInstance, string $column, $excludeId = null)
    {
        $this->eloquentInstance = $eloquentInstance;
        $this->column           = $column;
        $this->excludeId        = $excludeId;
    }

    public function passes($attribute, $value)
    {
        $query = $this->eloquentInstance->newQuery()
            ->where($this->column, $value);

        if (method_exists($this->eloquentInstance, 'getDeletedAtColumn')) {
            $query = $query->whereNull($this->eloquentInstance->getDeletedAtColumn());
        }
        if (isset($this->excludeId)) {
            $query = $query->where($this->eloquentInstance->getKeyName(), '<>', $this->excludeId);
        }

        return ! $query->exists();
    }

    public function message()
    {
        return ':attributeの値は既に存在しています。';
    }

    /**
     * @param int|string $id
     */
    public function setExcludeId($id): void
    {
        $this->excludeId = $id;
    }

    public function __toString()
    {
        return rtrim(sprintf('unique_not_soft_deleted:%s,%s', $this->eloquentInstance->getTable(), $this->column), ',');
    }
}
