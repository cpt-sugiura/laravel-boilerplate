import { TestFunction } from 'yup';

/**
 * 数値ルール
 */
export const makeNumericRule =
  (): TestFunction<string | null | undefined> =>
  (numeric: string | null | undefined): boolean => {
    if (numeric === '' || numeric == null) {
      return true;
    }

    return !!numeric.match(/^[-+]?[0-9]+\.?[0-9]*$/);
  };
