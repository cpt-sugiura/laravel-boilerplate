type StrGroup = 'lowers' | 'uppers' | 'numbers';
export const strRandom = (length = 16, useGroup: StrGroup[] = ['lowers', 'uppers', 'numbers']): string => {
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
/**
 * 文字列を n 文字目以降で ... に変換
 * @param str
 * @param length
 */
export const strCutForDisplay = (str: string, length = 12): string => {
  if (str.length <= length) {
    return str;
  }
  const cut = str.slice(0, length);

  return `${cut}…`;
};
export const isId = (id: unknown): boolean =>
  Boolean(typeof id === 'number' || (typeof id === 'string' && id.match(/^\d+$/)));

/** ある要素にテキストを入れた時の幅を取得 */
export const measureText = (text: string, element: HTMLElement): number => {
  const canvasEl = document.createElement('canvas');
  const context = canvasEl.getContext('2d');
  if (!context) {
    throw new Error('canvas の context 生成に失敗');
  }

  // パスをリセット
  context.beginPath();

  // フォントを取得
  // @see https://developer.mozilla.org/ja/docs/Web/API/Window/getComputedStyle
  context.font = window.getComputedStyle(element, null).getPropertyValue('font');

  // というテキストを描く場合の幅を取得 (a.widthに幅を表す数値が含まれる)
  return context.measureText(text).width;
};
