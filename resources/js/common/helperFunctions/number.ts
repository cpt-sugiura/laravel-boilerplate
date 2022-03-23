import { splitArrayByEqualSize } from '@/common/helperFunctions/arr';

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

export { numberFormat };
export { round };
