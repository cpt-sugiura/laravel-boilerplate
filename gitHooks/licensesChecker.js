// eslint-disable no-console
const childProcess = require('child_process');

const res = childProcess.execSync('license-checker --production');
const licenses = Array.from(new Set(
  res
    .toString()
    .split('\n')
    .filter((l) => l.includes('licenses: '))
    .map((l) => l.match(/.*licenses: (.*)/)[1])
)).sort()

console.log(licenses);
