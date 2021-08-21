import { TestFunction } from 'yup';

/**
 * パスワードルール
 */
export const makePasswordRule =
  (): TestFunction<string | null | undefined> =>
  (password: string | null | undefined): boolean => {
    if (password === '' || password == null) {
      return true;
    }

    if (password.length < 8) {
      return false;
    }
    return !!password.match(/^[a-zA-Z0-9!"#$%&'()-=^~\\|@`[{;+:*\]},<.>/?_]*$/);
  };
