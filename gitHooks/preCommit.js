// eslint-disable no-console
const childProcess = require('child_process');
const fs = require('fs');
const path = require('path');
main();

function main() {
  console.log('---run preCommit.js---');
  gitBranch()
  gitLint();
  console.log('---end preCommit.js---');
  process.exit(0);
}

function gitBranch() {
  // gitのコマンドを実行
  const gitBranchStdOut = childProcess.execSync('git branch').toString();
  // git branchで次の様な文字列が標準出力される
  //
  // * develop
  //   master
  //
  // これの内 "* "から始まるものが現在のブランチ。これを正規表現で抜き出す
  const currentBranchName = gitBranchStdOut.match(/^\* (.*)$/m)[1];
  // 現在参照しているブランチがmasterであるならば
  if (currentBranchName === 'master') {
    console.error('');
    console.error('masterブランチにcommitしようとしています');
    console.error('');
    // process.exit(1);
  }
  // 現在参照しているブランチが特定の正規表現に一致しないならば
  const category = ['admin', 'user', 'database', 'event'].join('|');
  const branchRuleRegex = new RegExp(`master|develop|deploy/.*|feature/(${category})/.*`);
  if (!branchRuleRegex.test(currentBranchName)) {
    console.error('');
    console.error('ブランチ名が命名規則に反しています。ブランチ名を変更してください');
    console.error(`命名規則は次の正規表現です。${branchRuleRegex.toString()}`);
    console.error(`例: git branch -m ${currentBranchName} <新しいブランチ名>`);
    console.error('');
    console.error('適切なブランチ名が命名規則の元で作れない場合, /gitHooks/preCommit.js の編集を行ってください');
    console.error('');
    process.exit(1);
  }

}

function gitLint() {
  // gitのコマンドを実行してコミットされようとしているファイルパスを抽出
  const filePaths = childProcess
    .execSync('git diff --staged --diff-filter=ACMR --name-only -z').toString()
    // git diff --staged --diff-filter=ACMR --name-only -z で次の様な文字列が標準出力されます
    //.php-cs-fixer.php^@app/Http/Controllers/MemberAPI/ClientErrorLoggerController.php^@gitHooks/preCommit.js^@resources/js/@types/not-js-files.d.ts^@
    // ^@は \u0000 のヌル文字です
    // この実行結果を整形して有効なファイルパスの配列にします
    .replace(/\u0000$/, '')
    .split("\u0000")
    .filter(v => !!v)
  ;
  // 対象のファイル一覧を表示。このファイルパスを元に色々できます。
  console.log(filePaths)

  // package.json の中からこのスクリプト用の実行スクリプトセット app-lint-staged を抜き出す
  const p = JSON.parse(fs.readFileSync(path.join(__dirname, '..', 'package.json')).toString())
  if (!p['app-lint-staged']) {
    return;
  }
  // app-lint-staged中の設定に従ってファイルを振り分けて処理
  // ex.
  //     "app-lint-staged": {
  //         ".*\\.php": [
  //             "docker-compose run --rm app ./vendor/bin/php-cs-fixer fix -vvv --config ./.php-cs-fixer.php",
  //             "git add"
  //         ]
  //     },
  Object.keys(p['app-lint-staged']).forEach(regexStr => {
    const regex = new RegExp(regexStr.replace('*', '.*').replace(/([^\\])\./, '$1\\.'));
    filePaths.forEach(filePath => {
      if (!regex.test(filePath)) {
        return;
      }
      p['app-lint-staged'][regexStr].forEach(cmdStr => {
        console.log(`${cmdStr} "${filePath}"`)
        // package.json 内で定義されたコマンドの末尾にファイルパスを追記してコマンドを実行
        const res = childProcess.execSync(`${cmdStr} "${filePath}"`);
        console.log(res.toString())
      })
    })
  })
}
