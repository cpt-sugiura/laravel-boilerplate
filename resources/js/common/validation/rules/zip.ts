import { TestFunction } from 'yup';

/**
 * 郵便番号ルール
 */
export const makeZipRule =
  (): TestFunction<string | null | undefined> =>
  (zip: string | null | undefined): boolean => {
    if (zip === '' || zip == null) {
      return true;
    }

    return !!zip.match(/^\d{3}-?\d{4}$/);
  };
