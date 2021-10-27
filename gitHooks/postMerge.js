// eslint-disable no-console
console.log('---run postMerge.js---');

const childProcess = require('child_process');

// gitのコマンドを実行

const gitDiffStdOut = childProcess.execSync('git diff-tree -r --name-only --no-commit-id ORIG_HEAD HEAD').toString();
// 次の様な文字列が標準出力される
// gitHooks/postMerge.js
// package-lock.json
// package.json
// ファイル名に応じて何かして欲しい的なメッセージを表示する
if (!!gitDiffStdOut.match(/^(package-lock.json|package.json)$/m)) {
  console.log('package.json が更新されました。JavaScript のコードを新たに動かすには npm install をする必要があります');
}
if (!!gitDiffStdOut.match(/^(composer.lock|composer.json)$/m)) {
  console.log('composer.json が更新されました。PHP のコードを新たに動かすには docker-compose exec app composer install をする必要があります');
}
if (!!gitDiffStdOut.match(/^database\/migrations\/.*$/m)) {
  console.log('データベース定義が更新されました。新たに動かすには docker-compose exec app php artisan migrate をする必要があります');
}
if (!!gitDiffStdOut.match(/^database\/schemas\/default\.yml$/m)) {
  console.log(
    'データベース定義が一式更新されました。新たに動かすには docker-compose exec app php artisan migrate:fresh --seed をする必要があります'
  );
}
if (!!gitDiffStdOut.match(/^.*Dockerfile$/m)) {
  console.log(
    'Dockerコンテナ定義が更新されました。docker-compose up --force-recreate --build でコンテナを更新していただきたいです'
  );
}
