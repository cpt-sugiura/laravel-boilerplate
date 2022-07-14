<?php

namespace App\Library\Rules;

use App\Models\Eloquents\BaseEloquent;
use Illuminate\Contracts\Validation\Rule;

/**
 * 論理削除されていないレコードの範囲のみでの存在ルール
 * Class ExistsInNotSoftDeleted
 * @package App\Rules
 */
class ExistsInNotSoftDeleted implements Rule
{
    /** @var BaseEloquent unique ルールで参照するテーブルのインスタンス */
    protected BaseEloquent $eloquentInstance;
    /** @var string exists ルールを適用するカラム */
    protected string $column;
    /** @var string|int|null exists ルールから除外するレコードのID */
    protected $excludeId;

    /**
     * ExistsInNotSoftDeleted constructor.
     * @param BaseEloquent    $eloquentInstance exists ルールで参照するテーブルのインスタンス
     * @param string|null     $column           exists ルールを適用するカラム。デフォルトはインスタンス->getKeyName()
     * @param int|string|null $excludeId        exists ルールから除外するレコードのID
     */
    public function __construct(BaseEloquent $eloquentInstance, ?string $column = null, int|string $excludeId = null)
    {
        $this->eloquentInstance = $eloquentInstance;
        $this->column           = $column ?? $eloquentInstance->getKeyName();
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

        return $query->exists();
    }

    public function message()
    {
        return ':attributeの値は存在していません。';
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
        return rtrim(sprintf('exists_not_soft_deleted:%s,%s', $this->eloquentInstance->getTable(), $this->column), ',');
    }
}
