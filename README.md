# 開発環境
## Makefile
Makefile でよく実行する手順を定義しています。
```shell
make install
```
で環境ファイルや Docker の立ち上げをしています。しょっちゅう異常終了するので手作業でスクリプトの途中から再開することも多いです。

## Docker
docker-compose.yml によく使うコンテナの定義を書いています。特に使うのは app, mysql, mailhog です。
### app
web サイトを表示する PHP プログラムを動かすコンテナです。

使用しているフォルダ・ファイル構成は以下です。

- コンテナ内の /work/backend/ に本プログラム全体をマウントしています。

### mysql
データベースコンテナです。MySQL を使用しています。

使用しているフォルダ・ファイル構成は以下です。

- コンテナ内の /etc/mysql/conf.d/my.cnf が本システムの MySQL の設定ファイルです。
    - イメージ構築時にソースコードの /docker/mysql/my.cnf をコピーすることによって配置しています。
- コンテナ内の /var/log/mysql/ にソースコードの /docker/log/mysql/ をマウントしています。

### mailhog
メールをローカルで受け取って見れるソフトです。ISO-2022-JP形式が文字化けするのは使用です。

## Gitフック
simple-git-hooks でコミット時に自動で eslint, php-cs-fixer, stylelint が走る様になっています。

[toplenboren/simple-git-hooks： A simple git hooks manager for small projects](https://github.com/toplenboren/simple-git-hooks)

simple-git-hooks は次のコマンドで Git フックとして働きます。
```shell
npx simple-git-hooks
```

package.json の次を
```json
"pre-commit": "npx lint-staged && node gitHooks/preCommit.js",
```
次の様にすることで黙らせられます
```json
"pre-commit": "",
```

# PHP
## データベース定義
複数人で見るテスト環境へのアップロード前までは dacapo を使用しています。複数人で見る様になってからはデータを壊さないために通常の Laravel のマイグレーションを使用します。

[ucan-lab/laravel-dacapo： Laravel migration support tool, Always generate the latest migration files on schema.yml](https://github.com/ucan-lab/laravel-dacapo)

[Laravel × Dacapo で始める快適マイグレーション生活！ - Qiita](https://qiita.com/ucan-lab/items/8d23dc08fc5f964a3e72)

## モデル
モデルの置き場は app/Models/Eloquents 以下です。Eloquents の中に更にカテゴリ分け的にディレクトリを増やすこともあります。モデルに密に関わっているものはモデルクラスでなくとも皓kに起きます。

モデルは artisan コマンドでデータベースのテーブルを元に自動生成できます。
```shell
php artisan dump:model-from-db -t [テーブル名]
```

## テストデータ
テストデータは Laravel の用意したシーダーを使うほか、artisan コマンドでデータベースを元に雑に生成することもできます。
```shell
php artisan dev:make_test_data --all
```
ignore オプションでテーブルを無視できます。徐々にシーダーを増やして次の様に整えることが多いです。
```php
<?php

namespace Database\Seeders;

use Artisan;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Output\ConsoleOutput;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(CompanySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(AccountSeeder::class);
        $ignoreList = [
            'companies',
            'products',
            'accounts',
        ];
        $output     = new ConsoleOutput();
        Artisan::call('dev:make_test_data --all --ignore='.implode(',', $ignoreList), [], $output);
    }
}
``` 

## コントローラー等の生成
簡易な CRUD 用のコントローラー、リクエスト、画面を次コマンドで生成できます。かなり怪しい機能なので動かない時はコマンドの改善を行うか諦めて手製で作るかします。
```shell
php artisan dump:controller --class=[モデルクラス名]
# 例
php artisan dump:controller --class=Member
```

## コントローラー
ユーザー側、管理者側などで BaseHogeController を用意し、それを継承する形で定義しています。
### クラスとメソッドの置き方
どのメソッドが何のためにあるのか分からなくなるのを避けるために __invoke メソッドを用いて一クラス一アクションにしています。多少複雑ならば private メソッドで分割してコントローラー内に収めています。あまりに複雑であったり、複数箇所から使用される処理ならば UseCase/コンテキストにあった名前 と新しくディレクトリを増やしてそこにコードを書いています。例えば次の様にしています。

例は import と export がマッチングするとあるマッチングシステムで、管理者がマッチングを強制解除する時の処理です。マッチングの強制解除はバッチ処理でも自動で行われるため UseCase 以下に分割しています。
```php
<?php

namespace App\Http\Controllers\AdminBrowserAPI\Hoge;

use App\Http\Controllers\AdminBrowserAPI\AdminBrowserAPIBaseController;
use App\Http\HttpStatus;
use App\Models\Eloquents\Hoge\HogeMatchingRelation;
use App\UseCase\Hoge\MatchingForceRelease\Actions\MatchingSystemReleaseAction;
use App\UseCase\Hoge\MatchingForceRelease\Actions\HogeMatchingNotifySystemReleaseAction;
use Illuminate\Http\JsonResponse;
use Throwable;

class HogeMatchingRelationReleaseController extends AdminBrowserAPIBaseController
{
    /**
     * @param  string|int                            $importId
     * @param  string|int                            $exportId
     * @param  MatchingSystemReleaseAction           $matchingSystemReleaseAction
     * @param  HogeMatchingNotifySystemReleaseAction $notifyReleaseAction
     * @throws Throwable
     * @return JsonResponse
     */
    public function __invoke(
        $importId,
        $exportId,
        MatchingSystemReleaseAction $matchingSystemReleaseAction,
        HogeMatchingNotifySystemReleaseAction $notifyReleaseAction
    ): JsonResponse {
        /** @var HogeMatchingRelation $match */
        $match = HogeMatchingRelation::query()
            ->whereHogeImportRequestId($importId)
            ->whereHogeExportRequestId($exportId)
            ->firstOrFail();
        if ($match->matching_relation_status !== HogeMatchingRelation::MATCHING_RELATION_STATE_INIT) {
            return $this->makeErrorResponse('マッチング解除可能な状態でないマッチングペアです。', HttpStatus::UNPROCESSABLE_ENTITY);
        }

        return \DB::transaction(function () use ($match, $notifyReleaseAction, $matchingSystemReleaseAction) {
            $matchingSystemReleaseAction($match);
            $notifyReleaseAction($match, $match->hogeImportRequest);
            $notifyReleaseAction($match, $match->hogeExportRequest);

            return $this->makeSuccessResponse('マッチングを解除しました。');
        });
    }
}
```
```php
<?php

namespace App\UseCase\Hoge\MatchingForceRelease\Actions;

use App\Models\Eloquents\Hoge\HogeMatchingRelation;
use DB;

/**
 * 強制解除と期限切れによる解除
 */
class MatchingSystemReleaseAction
{
    public function __invoke(HogeMatchingRelation $match): HogeMatchingRelation
    {
        assert($match->matching_relation_status !== HogeMatchingRelation::MATCHING_RELATION_STATE_ANNUL,
            '既に強制解除済みのマッチングに対して強制解除を行おうとしました');
        assert($match->matching_relation_status !== HogeMatchingRelation::MATCHING_RELATION_STATE_RELEASE,
            '既に正常解除済みのマッチングに対して強制解除を行おうとしました');
        assert($match->matching_import_status !== HogeMatchingRelation::MATCHING_IMPORT_STATE_DISAGREE,
            '既にほげが欲しいが見合わせ済みのマッチングに対して強制解除を行おうとしました');
        assert($match->matching_export_status !== HogeMatchingRelation::MATCHING_EXPORT_STATE_DISAGREE,
            '既にほげを出したいが見合わせ済みのマッチングに対して強制解除を行おうとしました');

        DB::transaction(function () use ($match) {
            $now = now();
            $match->matching_relation_status = HogeMatchingRelation::MATCHING_RELATION_STATE_ANNUL;
            $match->matching_relation_change_status_at = $now;
            $match->remandHogeVolumeTransfer($match->hogeImportRequest, $match->hogeExportRequest);
            $match->save();

            $imp = $match->hogeImportRequest;
            ($imp->end_date < $now) ? $imp->changeStateToExpired() : $imp->changeStateToWait();
            $imp->save();

            $exp = $match->hogeExportRequest;
            ($exp->end_date < $now) ? $exp->changeStateToExpired() : $exp->changeStateToWait();
            $exp->save();
        });

        return $match;
    }
}
```
```php
<?php

namespace App\UseCase\Hoge\MatchingForceRelease\Actions;

use App\Library\FireBase\Contracts\TranslatableToCloudMessage;
use App\Library\FireBase\FirebaseCloudMessage;
use App\Models\Eloquents\NotificationModel;
use App\Models\Eloquents\Hoge\HogeMatchingRelation;
use App\UseCase\Hoge\Mail\HogeMailBladeHelper;
use App\UseCase\Hoge\Matching\MatchingNotifiableEloquentContract;
use App\UseCase\Hoge\MatchingForceRelease\Notify\MatchingReleaseCloudMessage;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Mail;

class HogeMatchingNotifySystemReleaseAction
{
    /**
     * @param  HogeMatchingRelation               $match
     * @param  MatchingNotifiableEloquentContract $tgt
     * @throws FirebaseException
     * @throws MessagingException
     */
    public function __invoke(HogeMatchingRelation $match, MatchingNotifiableEloquentContract $tgt)
    {
        assert(
            $match->matching_relation_status === HogeMatchingRelation::MATCHING_RELATION_STATE_ANNUL,
            '強制停止でないマッチングに対してマッチング解除通知が送られようとされました'
        );
        $msg = $this->sendFcm($match, $tgt);
        $this->sendMail($match, $tgt);
        $this->saveDatabase($match, $tgt, $msg);
    }

    /**
     * @param  HogeMatchingRelation               $match
     * @param  MatchingNotifiableEloquentContract $tgt
     * @throws FirebaseException
     * @throws MessagingException
     * @return TranslatableToCloudMessage
     */
    private function sendFcm(HogeMatchingRelation $match, MatchingNotifiableEloquentContract $tgt): TranslatableToCloudMessage
    {
        $fcm = FirebaseCloudMessage::makeService();
        $msg = new MatchingReleaseCloudMessage($match, $tgt);
        $fcm->setMessageFromFcmNotifiable($msg);
        $fcm->sendMulticast($tgt->matchingEstablishNotifyDeviceTokens());

        return $msg;
    }

    private function sendMail(HogeMatchingRelation $match, MatchingNotifiableEloquentContract $tgt): void
    {
        if ($match->hogeImportRequest->member_id === $tgt->matchingNotifyTargetMember()->member_id) {
            $view = \View::file(
                __DIR__.'/../Notify/Mail/MatchingNotifyReleaseToImport.blade.php',
                HogeMailBladeHelper::makeViewDataBase($match)
            );
        } else {
            $view = \View::file(
                __DIR__.'/../Notify/Mail/MatchingNotifyReleaseToExport.blade.php',
                HogeMailBladeHelper::makeViewDataBase($match)
            );
        }

        Mail::raw($view->render(), function ($message) use ($tgt) {
            $message->to($tgt->matchingNotifyEmailAddress())
                ->from(config('mail.from.address'))
                ->subject('【DoMatch】マッチング候補の強制解除');
        });
    }

    private function saveDatabase(HogeMatchingRelation $match, MatchingNotifiableEloquentContract $tgt, TranslatableToCloudMessage $fcm): void
    {
        $notify                       = new NotificationModel();
        $notify->title                = $fcm->fcmTitle();
        $notify->message              = $fcm->fcmMessage();
        $notify->matching_relation_id = $match->getKey();
        $notify->member_id            = $tgt->matchingNotifyTargetMember()->getKey();
        $notify->category             = NotificationModel::CATEGORY_MATCH_RELEASE;
        $notify->save();
    }
}
```
### APIのレスポンスの返し方

JSON レスポンスの型が次の様になる様に app/Http/Controllers/ApiResponseTrait.php 内の makeXXXResponseメソッドを結果をｈ返しています。
```typescript
type Response = {
    success: boolean;
    body   : T;
    message: string,
}
```
あるモデルのレスポンスの型が一定になる様に app/Http/Controllers/AdminBrowserAPI/Presenters/AdminPresenter.php の様なプレゼンター層を設けています。コントローラー内のコードで次の様にしてこれを返しています。
```php
<?php

namespace App\Http\Controllers\AdminBrowserAPI\Admin;

use App\Http\Controllers\AdminBrowserAPI\BaseAdminBrowserAPIController;
use App\Http\Controllers\AdminBrowserAPI\Presenters\AdminPresenter;
use App\Models\Eloquents\Admin\Admin;
use Illuminate\Http\JsonResponse;

class AdminShowController extends BaseAdminBrowserAPIController
{
    /**
     * @param  int|string   $adminId
     * @return JsonResponse
     */
    public function __invoke(int|string $adminId): JsonResponse
    {
        $admin = Admin::findOrFail($adminId);

        return $this->makeResponse(new AdminPresenter($admin));
    }
}

```

## 検索クエリ
/app/Models/Search 以下にクエリの構築を定義し、コントローラー内で
```php
<?php

namespace App\Http\Controllers\AdminBrowserAPI\Admin;

use App\Http\Controllers\AdminBrowserAPI\BaseAdminBrowserAPIController;
use App\Http\Presenters\PaginatorPresenter;
use App\Http\Requests\SearchRequest;
use App\Models\Search\AdminAPI\Admin\AdminSearchQueryBuilder;
use Illuminate\Http\JsonResponse;

class AdminSearchController extends BaseAdminBrowserAPIController
{
    public function __invoke(SearchRequest $request): JsonResponse
    {
        $result = (new AdminSearchQueryBuilder())
            ->search($request->search, $request->orderBy)
            ->paginate($request->perPage);

        return $this->makeResponse((new PaginatorPresenter($result))->toArray());
    }
}

```
の様にして使っています。

## ヘルパー関数
独自定義のヘルパー関数を /app/helpers.php 以下に置いています。コンテキスト問わず使われる小さな処理は大体ここに置いています。SQLのデバッグの際は次を使うことがしばしばあります。
```php
refresh_query_log();
// SQL 発行処理
dd(get_query_log());// SQL発行処理の部分だけのSQLが表示される
```

## デバッグ
ブラウザ拡張やアドオンでは clockwork を使用しています。

[itsgoingd/clockwork： Clockwork - php dev tools in your browser - server-side component](https://github.com/itsgoingd/clockwork)

# JavaScript
React + TypeScript を使っています。デザインフレームワークには Material UI を使用しています。

[MUI： The React component library you always wanted](https://mui.com/)

## _WhenNotLoginの様なディレクトリ分け
ログインしていない場合、ユーザー画面、管理者画面など見れるべきでない部分はディレクトリを分けてビルドも分けています。定義は /webpack.mix.js にあります。どこでも共通で呼ばれるコードが resources/js/common です。

ログイン後に読まれる JavaScript ファイルは /storage/app/assets/admin/js/app.js の様に /storage/app/assets 以下に配置され、/routes/admin_web.php にある次のコードの様にしてログイン済みの状態でのみ外部から読める様になります。
```php
Route::middleware('auth:admin_web')->group(static function () {
    Route::get('storage/{any}', static function ($any) {
        $contentType = preg_match('/\.css$/', $any) ? 'text/css; charset=UTF-8' : null;
        $headers     = [];
        if ($contentType) {
            $headers['Content-Type'] = $contentType;
        }

        if (Storage::disk(Admin::ASSETS_STORAGE_DISK_KEY)->exists($any)) {
            return Storage::disk(Admin::ASSETS_STORAGE_DISK_KEY)->response($any, null, $headers);
        }
        abort(404);
    })->where('any', '.*')->name('storage');
});

```

## pages, repository, hook, context, component
名前の通りに /技術単位/カテゴリ/ファイル ぐらいに分けています。これはかなり緩く、そのページでしか使わない、そのコンポーネントの中でしか使わない、といった時は次の様にモジュール的な分け方をする時もあります。
```
resources\js\account\pages\carDiagram
│  CarDiagramPage.scss
│  CarDiagramPage.tsx
│  CarDiagramPageType.ts
│  droppableId.ts
│  
├─component
│      CarDiagramScheduleCategoryArea.tsx
│      CarDiagramTableHeaderCell.tsx
│      CarDiagramUnselectedCarArea.tsx
│      
└─hooks
        useMakeTimeList.tsx
        useCarList.tsx
```

## @types
複雑だったり、タイプヒントが合った方が良さそうな API レスポンスは /resources/js/@types/API 以下に配置し、次の様に AxiosResponse<BaseResponse<レスポンスの型>> として使います。
```typescript
import { useEffect, useState } from 'react';
import { AxiosResponse } from 'axios';
import { BaseResponse } from '@/@types/API/BaseResponse';
import { useAccountAxios } from '@/account/hook/API/useAccountAxios';
import { MenuPermissionKeys, PermissionCrud } from '@/account/hook/MenuPermission/useMenuPermissionsOfLoginAccount';
import { AccountMenuPermissionListResponseBody, AccountMenuPermissionRecord } from '@/@types/API/AccountMenuPermission';

export const useMenuPermissionsOnDB = () => {
    const [permissions, setPermissions] =
        useState<Record<MenuPermissionKeys, PermissionCrud<AccountMenuPermissionRecord>>>();
    const { isLoading, axiosInstance } = useAccountAxios();
    useEffect(() => {
        axiosInstance
            .get('/account_menu_permission/permission_list')
            .then((r: AxiosResponse<BaseResponse<AccountMenuPermissionListResponseBody>>) => {
                setPermissions(r.data.body.permissions);
            });
    }, []);

    return {
        isLoading,
        permissions,
    };
};
```
