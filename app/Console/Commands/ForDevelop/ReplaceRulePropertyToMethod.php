<?php

namespace App\Console\Commands\ForDevelop;

use App\Console\BaseCommand;

class ReplaceRulePropertyToMethod extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:replace-model-rules {className}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'モデル中の $rules プロパティを public static function に置き換え';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $modelFilePath = config('infyom.laravel_generator.path.model').DIRECTORY_SEPARATOR.$this->argument(
            'className'
        ).'.php';

        $content         = file_get_contents($modelFilePath);
        $replacedContent = $this->replacer($content);

        file_put_contents($modelFilePath, $replacedContent);
    }

    /**
     * rules プロパティを rules メソッドに置き換え
     * @param  string $classSrcCode
     * @return string
     */
    private function replacer(string $classSrcCode): string
    {
        // マッチされた部分に限って色々操作するために preg_replace_callback を使用
        // preg_replace_callback はマッチ部を引数に取る関数と正規表現を用いて文字列を置換する
        return preg_replace_callback(
            '/\$rules = (\[[\S\s]*?\]);/', // $rulesの値だけをグループ化。\S\sによって[から始まり、改行コードも含めた任意文字が続いて];で終わると表現
            static function ($matches) {
                // メソッドの大枠をヒアドキュメントで定義してグループ化した元々の$rulesの値をリターンにセット
                $functionCode = <<<EOF
function rules(): array
    {
        return {$matches[1]};
    }
EOF;
                // Laravel の rule 定義はパイプ区切り文字列よりも配列にしたほうが好きな派なので配列化
                // これは配列化の前準備としてパイプ区切りをカンマ区切りに変えている
                $functionCode = str_replace('|', "','", $functionCode);

                // カンマ区切りの文字列が並んでいるだけのPHPとして不正な構文を配列にする
                return preg_replace('/=> (.*?)[\r\n]*$/m', '=> [$1],', $functionCode);
            },
            $classSrcCode
        );
    }
}
