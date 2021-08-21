type StrGroup = 'lowers' | 'uppers' | 'numbers';
const strRandom = (length = 16, useGroup: StrGroup[] = ['lowers', 'uppers', 'numbers']): string => {
  const lowers = 'abcdefghijklmnopqrstuvwxyz';
  const uppers = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  const numbers = '0123456789';

  const chars = [];
  useGroup.includes('lowers') && chars.push(lowers);
  useGroup.includes('uppers') && chars.push(uppers);
  useGroup.includes('numbers') && chars.push(numbers);

  const randomChars = chars.join('');

  return Array.from(crypto.getRandomValues(new Uint8Array(length)))
    .map((n) => randomChars[n % randomChars.length])
    .join('');
};

const urlToFile = async (url: string): Promise<File> => {
  return fetch(url)
    .then((response) => response.blob())
    .then((blob) => new File([blob], url.replace(/.*([^/]*$)/, '')));
};

const anyTo01 = (any: unknown): 0 | 1 => {
  if (typeof any === 'string') {
    if (any === 'false' || any === '0') {
      return 0;
    }
  }
  return any ? 1 : 0;
};

const arrUniq = <T>(array: T[]): T[] => {
  return Array.from(new Set(array));
};

/**
 * 文字列を n 文字目以降で ... に変換
 * @param str
 * @param length
 */
const strCutForDisplay = (str: string, length = 12): string => {
  if (str.length <= length) {
    return str;
  }
  const cut = str.slice(0, length);

  return `${cut}…`;
};
/**
 * 任意桁数での四捨五入
 * @param numberArg
 * @param precisionArg
 */
const round = (numberArg: number, precisionArg: number): number => {
  const shift = (number: number, precision: number, reverseShift: boolean) => {
    if (reverseShift) {
      precision = -precision;
    }
    const numArray = ('' + number).split('e');
    return +(numArray[0] + 'e' + (numArray[1] ? +numArray[1] + precision : precision));
  };
  return shift(Math.round(shift(numberArg, precisionArg, false)), precisionArg, true);
};
import format from 'date-fns/format';
const dateFormatToMySqlDate = (date: Date): string => {
  return format(date, 'yyyy-MM-dd');
};
/**
 * @param year
 * @param month 1-12
 */
const getMonthCalendar = (year: number, month: number): Date[][] => {
  const lastDate = new Date(year, month, 0).getDate(); // 指定した年月の末日
  const calendar: Date[][] = []; // カレンダーの情報を格納
  let row = 0;
  for (let i = 1; i <= lastDate; i++) {
    const d = new Date(year, month - 1, i);
    if (calendar[row] === undefined) {
      calendar[row] = [];
    }
    calendar[row].push(d);
    if (d.getDay() === 6) {
      row++;
    }
  }
  return calendar;
};

const isId = (id: unknown): boolean => Boolean(typeof id === 'number' || (typeof id === 'string' && id.match(/^\d+$/)));

/**
 * PHP の number_format を JavaScript で実装
 * @see ドキュメント https://www.php.net/manual/ja/function.number-format.php
 * @see 本家 https://github.com/php/php-src/blob/07fa13088e1349f4b5a044faeee57f2b34f6b6e4/ext/standard/math.c#L1011
 * @param {Number|String} num 数値を表現する文字列でもOK
 * @param {Number|String} decimals 数値を表現する文字列でもOK
 * @param {String} decimalSeparator
 * @param {String} thousands_separator
 */
function numberFormat(num: number | string, decimals = 0, decimalSeparator = '.', thousands_separator = ','): string {
  let i;
  num = +num;
  decimals = +decimals < 0 ? 0 : decimals;
  // 少数
  // 文字列で数値を構築（誤差対策も兼ねます）
  let strnum: string = round(num, decimals).toString();
  let addZero = '';
  if (!strnum.toString().includes('.')) {
    if (decimals > 0) {
      addZero += '.';
    }
    for (i = 0; i < decimals; i++) {
      addZero += '0';
    }
  } else {
    const decimal = strnum.toString().split('.')[1];
    for (i = 0; i < decimals - decimal.length; i++) {
      addZero += '0';
    }
  }
  strnum = `${strnum}${addZero}`.replace('.', decimalSeparator); // 小数点を置き換え

  // 千の位区切り
  let sign = ''; // 後の文字列操作で符号が邪魔なので避難
  if (num < 0) {
    strnum = strnum.slice(1);
    sign = '-';
  }
  const integerSide: string[] = strnum.split(decimalSeparator)[0].split(''); // 整数部を配列形式で抜き出し
  const integerSideWithComma = splitArrayByEqualSize<string>(integerSide.reverse(), 3) // 1の位から数えるためにchar[]を反転
    .map((t) => t.join('')) // 3桁ずつまとめる
    .join(thousands_separator)
    .split('')
    .reverse() // 文字列に復元
    .join('');

  return [sign, integerSideWithComma, decimals === 0 ? '' : decimalSeparator, strnum.split(decimalSeparator)[1]].join(
    ''
  );
}

/**
 * 配列を要素 n 個の複数の小さい配列に分割。余りは最後の配列に入れる（最後の配列だけ n より小さいことがある）。
 * @param {any[]} arr
 * @param {Number} n
 * @return {any[][]}
 */
function splitArrayByEqualSize<T>(arr: T[], n: number): T[][] {
  return arr.reduce((pre: T[][], c: T, i): T[][] => (i % n ? pre : [...pre, arr.slice(i, i + n)]), []);
}

export {
  strRandom,
  urlToFile,
  anyTo01,
  arrUniq,
  strCutForDisplay,
  round,
  getMonthCalendar,
  dateFormatToMySqlDate,
  isId,
  numberFormat,
};
