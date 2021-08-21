<?php

namespace App\Console\Commands\ForDevelop;

use App\Console\BaseCommand;
use ErrorException;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;

class LangTranslateDumper extends BaseCommand
{
    protected $name        = 'dev:lang-translate-dump';
    protected $description = '日本語から翻訳した言語ファイルをダンプする';

    protected function getOptions()
    {
        return [
            ['react', 'r', InputOption::VALUE_NONE, 'react側翻訳'],
            ['laravel', 'l', InputOption::VALUE_NONE, 'laravel側翻訳'],
        ];
    }

    /**
     * @throws ErrorException
     */
    public function handle(): void
    {
        $translator = $this->getTranslator();
        if (! $this->option('react') && ! $this->option('laravel')) {
            $this->warn('no action. plz -r or -l');

            return;
        }
        $this->option('laravel') && $this->handleLaravel($translator);
        $this->option('react') && $this->handleReact($translator);
    }

    /**
     * 翻訳用ライブラリを準備
     * @see https://github.com/Stichoza/google-translate-php
     * @return GoogleTranslate
     */
    public function getTranslator(): GoogleTranslate
    {
        $tr = new GoogleTranslate();
        $tr->setSource('ja');
        $tr->setSource();
        $tr->setTarget('en');

        return $tr;
    }

    /**
     * 多次元配列のメッセージを Google 翻訳に再帰的にかける
     * @param GoogleTranslate        $translator
     * @param float|int|string|array $message
     *@throws ErrorException
     * @return float|int|string|array|null
     */
    private function recursiveTranslate(GoogleTranslate $translator, $message)
    {
        if (is_string($message)) {
            // 文字列ならば翻訳すべきメッセージとして処理
            // :hoge の置き換え文字列を翻訳対象から退避
            $this->info('pre translate: '.$message);
            preg_match_all('/:[a-zA-Z0-9]+/', $message, $replacers);
            $replacedMessage = str_replace(
                $replacers[0],
                array_map(static fn ($i) => "@$i", range(0, count($replacers))),
                $message
            );
            // 翻訳
            $translateMsg = $translator->translate($replacedMessage);
            // 退避した翻訳文字列を復元
            /** @var string $ret */
            $ret = str_replace(
                array_map(static fn ($i) => "@ $i", range(0, count($replacers))),
                $replacers[0],
                $translateMsg
            );
            /** @noinspection CascadeStringReplacementInspection */
            $ret = str_replace(
                ['【have to】', '(seconds)', '(second)', '$ {Path}', 'Password confirmation)'],
                ['【require】', '(sec)', '(sec)', '${path}', 'Password(confirmation)'],
                $ret
            );
            $this->warn('post translate: '.$ret);
            sleep(1); // Google 翻訳への過剰アクセス防止

            return $ret;
        }
        if (is_int($message) || is_float($message) || $message === null) {
            // 数値や空ならばそのまま返す
            return $message;
        }
        if (is_array($message)) {
            // 配列ならば更に配列の深いところへ潜る
            foreach ($message as $key => $item) {
                $message[$key] = $this->recursiveTranslate($translator, $item);
            }
        }

        // 全て翻訳し終わった結果を返す
        return $message;
    }

    /**
     * @param  GoogleTranslate $translator
     * @throws ErrorException
     */
    public function handleLaravel(GoogleTranslate $translator): void
    {
        // メッセージファイルへのフルパスを全て取得
        foreach (glob(resource_path('lang/ja/*')) as $file) {
            // メッセージファイルが配列なので require で中身を実体にできます。
            $messages = require $file;
            // メッセージファイルの中身を翻訳
            $translatedMessages = $this->recursiveTranslate($translator, $messages);
            // 翻訳結果配列を PHP ソースコード文字列に変換
            $arrStr = var_export($translatedMessages, true);
            // メッセージファイルとして完成された PHP ソースコード文字列を生成
            $phpCode = <<<CODE
<?php

return {$arrStr};

CODE;
            // 翻訳結果を翻訳対象言語ファイルとして保存
            file_put_contents(str_replace('/ja/', '/en/', $file), $phpCode);
            $this->info('output: '.str_replace('/ja/', '/en/', $file));
        }
    }

    /**
     * @param  GoogleTranslate $translator
     * @throws ErrorException
     */
    public function handleReact(GoogleTranslate $translator): void
    {
        $messages = Yaml::parseFile(resource_path('js/lang/messages/ja.yml'));
        // メッセージファイルの中身を翻訳
        /** @var array $translatedMessages */
        $translatedMessages                         = $this->recursiveTranslate($translator, $messages);
        $translatedMessages['datepicker']['format'] = 'MMM d, yyyy HH:mm';
        file_put_contents(resource_path('js/lang/messages/en.yml'), Yaml::dump($translatedMessages, 1e5, 2, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE));
        $this->info('output: '.resource_path('js/lang/messages/en.yml'));
    }
}
