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
    process.exit(1);
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
  // gitのコマンドを実行
  const gitStatusStdOut = childProcess.execSync('git status -s').toString();
  // git status -sで次の様な文字列が標準出力される
  // A  a.txt
  // M app/Library/Omise/Charge/Mock/OmiseChargeMock.php
  // M database/seeders/EventWebOrderSeeder.php
  // M package.json
  // これらの内 A, M が今回変更されて色々処理すべきファイル。これを正規表現で抜き出す
  const filePaths = gitStatusStdOut
    .split("\n")
    .map(line => line.match(/^\s*[AM] +(.*)$/)?.[1])
    .filter(v => !!v)
  ;
  console.log(filePaths)
  const p = JSON.parse(fs.readFileSync(path.join(__dirname, '..', 'package.json')).toString())
  if (!p['app-lint-staged']) {
    return;
  }
  Object.keys(p['app-lint-staged']).forEach(regexStr => {
    const regex = new RegExp(regexStr.replace('*', '.*').replace(/([^\\])\./, '$1\\.'));
    filePaths.forEach(filePath => {
      if (!regex.test(filePath)) {
        return;
      }
      p['app-lint-staged'][regexStr].forEach(cmdStr => {
        console.log(`${cmdStr} "${filePath}"`)
        const res = childProcess.execSync(`${cmdStr} "${filePath}"`);
        console.log(res.toString())
      })
    })
  })
}
