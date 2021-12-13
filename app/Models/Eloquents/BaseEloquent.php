<?php

namespace App\Models\Eloquents;

use App\Http\Presenters\SelectOptionsPresenter;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Scope;
use Str;

/**
 * 認証機能持ち以外のモデルのベース
 * Class BaseEloquent
 * @package App\Models\Eloquents
 * @mixin Model
 */
abstract class BaseEloquent extends Model
{
    use BulkInsert;

    /** このクラスの名前。外部に見せる等で日本語を想定 */
    public static function getNaturalLanguageName(): string
    {
        $connection = (new static())->getConnection();
        $table      = $connection->getTablePrefix().(new static())->getTable();

        return $connection->getDoctrineSchemaManager()
            ->listTableDetails($table)
            ->getComment() ?: class_basename(static::class);
    }

    /**
     * 選択肢に使いやすい感じにフォーマットするプレゼンタを返す.
     * カラム名をマジックナンバー的に使うのに注意
     * @param  string                 $label
     * @param  string|null            $orderBy
     * @param  string                 $ascOrDesc
     * @return SelectOptionsPresenter
     */
    public static function createSelectOptionsPresenter(
        $label = 'name',
        $orderBy = null,
        $ascOrDesc = 'asc'
    ): SelectOptionsPresenter {
        $orderBy = $orderBy ?? (new static())->getKeyName();

        return new SelectOptionsPresenter(
            static::query()->orderBy($orderBy, $ascOrDesc)->get(),
            $label,
            (new static())->getKeyName()
        );
    }

    /**
     * HasManyThroughの引数割り当ての誤りっぷりがひどいので作成
     * @param  string         $tgtClass                       関係先テーブルクラス
     * @param  string         $throughClass                   通る中間テーブルクラス
     * @param  string|null    $throughHasThisPrimaryKeyColumn 中間テーブルの持つ$thisの主キーを指すカラム
     * @param  string|null    $throughHasTgtPrimaryKeyColumn  中間テーブルの持つ関係先の主キーを指すカラム
     * @return HasManyThrough
     */
    public function hasManyThroughEasyWrapper(
        string $tgtClass,
        string $throughClass,
        ?string $throughHasThisPrimaryKeyColumn = null,
        ?string $throughHasTgtPrimaryKeyColumn = null
    ): HasManyThrough {
        return $this->hasManyThrough(
            $tgtClass,
            $throughClass,
            $throughHasThisPrimaryKeyColumn ?? $this->getKeyName(),
            (new $tgtClass())->getKeyName(),
            $this->getKeyName(),
            $throughHasTgtPrimaryKeyColumn ?? (new $tgtClass())->getKeyName(),
        );
    }

    /**
     * HasOneThroughの引数割り当ての誤りっぷりがひどいので作成
     * @param  string        $tgtClass                       関係先テーブルクラス
     * @param  string        $throughClass                   通る中間テーブルクラス
     * @param  string|null   $throughHasThisPrimaryKeyColumn 中間テーブルの持つ$thisの主キーを指すカラム
     * @param  string|null   $throughHasTgtPrimaryKeyColumn  中間テーブルの持つ関係先の主キーを指すカラム
     * @return HasOneThrough
     */
    public function hasOneThroughEasyWrapper(
        string $tgtClass,
        string $throughClass,
        ?string $throughHasThisPrimaryKeyColumn = null,
        ?string $throughHasTgtPrimaryKeyColumn = null
    ): HasOneThrough {
        return $this->hasOneThrough(
            $tgtClass,
            $throughClass,
            $throughHasThisPrimaryKeyColumn ?? $this->getKeyName(),
            (new $tgtClass())->getKeyName(),
            $this->getKeyName(),
            $throughHasTgtPrimaryKeyColumn ?? (new $tgtClass())->getKeyName(),
        );
    }

    /**
     * モデルを新しい、既存ではないインスタンスにクローンします。
     *
     * @param  array|null                               $except
     * @return \App\Models\Eloquents\Event\Order\static
     */
    public function replicate(array $except = null): self
    {
        // クローンされたくないプロパティ名を列挙。
        // laravel_through_key: HasManyThroughメソッドを実行すると引っ付いてくる中間テーブルのキー
        $defaults = [
            'laravel_through_key',
        ];
        $except = $except ?? $defaults;

        return parent::replicate($except);
    }

    /**
     * Get the casts array.
     * スネークケース、キャメルケースの片方のキャスト定義でもう片方もキャストする
     *
     * @return array
     */
    public function getCasts(): array
    {
        $baseCasts   =parent::getCasts();
        $returnCasts = [];
        foreach ($baseCasts as $key => $baseCast) {
            $returnCasts[Str::snake($key)] = $baseCast;
            $returnCasts[Str::camel($key)] = $baseCast;
        }

        return $returnCasts;
    }

    /**
     * HasManyThrough で使っているリレーションをまとめて更新するメソッド
     * @param HasManyThrough       $hasManyThrough      Eloquentの多対多リレーションを表現したクラス
     * @param array                $newThroughTgtIds    更新後のhasManyThroughで参照しているキー全て
     * @param string|string[]|null $reloadRelationNames
     */
    public function updateHasManyThroughRelations(HasManyThrough $hasManyThrough, array $newThroughTgtIds, array | string $reloadRelationNames=null): void
    {
        $throughModel                        = $hasManyThrough->getParent();
        $thisKeyInThroughModel               = $hasManyThrough->getFirstKeyName();
        $hasManyThroughTgtKeyInThroughModel  = $hasManyThrough->getSecondLocalKeyName();

        $alreadyExistThroughModelIds = $hasManyThrough->getParent()->newQuery()
            ->where($thisKeyInThroughModel, $this->getKey())
            ->get()->pluck($hasManyThrough->getForeignKeyName());

        collect($newThroughTgtIds)->whereNotIn(null, $alreadyExistThroughModelIds)
            ->each(
                function ($newRelateThroughTgtId) use ($thisKeyInThroughModel, $hasManyThroughTgtKeyInThroughModel, $throughModel) {
                    $newThroughModel = new $throughModel();
                    $newThroughModel->$thisKeyInThroughModel = $this->getKey();
                    $newThroughModel->$hasManyThroughTgtKeyInThroughModel = $newRelateThroughTgtId;
                    $newThroughModel->saveOrFail();
                }
            );

        $deleteThroughModelIds = $alreadyExistThroughModelIds->whereNotIn(null, $newThroughTgtIds);

        $throughModel->newQuery()->whereIn(
            $throughModel->getTable().'.'.$hasManyThrough->getForeignKeyName(),
            $deleteThroughModelIds
        )->delete();
        // $thisがリレーションを既にロード済みの場合、データベースを変更してもリレーションが変更されません。
        // load メソッドで明示的にリレーションをロードすることで再読み込みしてリレーションをデータベースに即した形にできます。
        $reloadRelationNames && $this->load($reloadRelationNames);
    }

    public static function removeGlobalScope($scope): void
    {
        if (is_string($scope)) {
            unset(static::$globalScopes[static::class][$scope]);

            return;
        }

        if ($scope instanceof Closure) {
            unset(static::$globalScopes[static::class][spl_object_hash($scope)]);

            return;
        }

        if ($scope instanceof Scope) {
            unset(static::$globalScopes[static::class][get_class($scope)]);

            return;
        }

        throw new \InvalidArgumentException('Global scope must be an instance of Closure or Scope.');
    }
}
