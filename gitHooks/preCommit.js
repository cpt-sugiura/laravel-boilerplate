// eslint-disable no-console
const childProcess = require('child_process');
console.log('---run preCommit.js---');
// gitのコマンドを実行
const gitBranchStdOut = childProcess.execSync('git branch').toString();
// git branchで次の様な文字列が標準出力される
//
// * develop
//   master
//
// これの内 "* "から始まるものが現在のブランチ。これを正規表現で抜き出す
const currentBranchName = gitBranchStdOut.match(/^\* (.*)$/m)[1];
// 現在参照しているブランチが特定の正規表現に一致しないならば
const category = [
  'admin',
  'user',
  'database',
].join('|');
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
