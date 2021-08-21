<?php

namespace App\Console\Commands\ForDevelop;

use App\Console\BaseCommand;
use App\Models\Eloquents\BaseEloquent;

class AppendSoftDeleteToModel extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:append-model-soft-delete {classNamePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'モデル中の deleted_at カラムに対応する softDelete trait の use を追加';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        /** @var BaseEloquent $modelNamePath */
        $modelNamePath = $this->argument('classNamePath');
        /** @var BaseEloquent $modelInstance */
        $modelInstance   = new $modelNamePath();
        $deletedAtColumn = $modelInstance->getConnection()->getDoctrineColumn($modelInstance->getTable(), 'deleted_at');
        if (! $deletedAtColumn) {
            $this->error($modelInstance->getTable().'.deleted_at カラムが見つかりませんでした。');

            return;
        }
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($modelInstance), true)) {
            $this->error('既に SoftDeletes は use されています');

            return;
        }

        $modelFilePath   = config('infyom.laravel_generator.path.model').DIRECTORY_SEPARATOR.class_basename($modelNamePath).'.php';
        $content         = file_get_contents($modelFilePath);
        $replacedContent = $this->appender($content);

        file_put_contents($modelFilePath, $replacedContent);
    }

    private function appender(string $content): string
    {
        $newContent = preg_replace('/(class [^{]*{)/', "$1\nuse SoftDeletes;", $content);

        return preg_replace('/(namespace \S+;\n)/', "$1\nuse Illuminate\Database\Eloquent\SoftDeletes;", $newContent);
    }
}
