export const arrUniq = <T>(array: T[]): T[] => {
  return Array.from(new Set(array));
};

/**
 * 配列を要素 n 個の複数の小さい配列に分割。余りは最後の配列に入れる（最後の配列だけ n より小さいことがある）。
 * @param {any[]} arr
 * @param {Number} n
 * @return {any[][]}
 */
export const splitArrayByEqualSize = <T>(arr: T[], n: number): T[][] => {
  return arr.reduce((pre: T[][], c: T, i): T[][] => (i % n ? pre : [...pre, arr.slice(i, i + n)]), []);
};

/**
 * 一定配列を任意ステップで刻んだ配列を生成
 * @param min
 * @param max
 * @param step
 */
export const range = (min: number, max: number, step = 1): number[] => {
  return Array.from({ length: (max - min + step) / step }, (v, k) => min + k * step);
};
