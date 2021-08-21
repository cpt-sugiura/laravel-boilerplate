import { TestFunction } from 'yup';

/**
 * 電話番号ルール
 */
export const makePhoneNumberRule =
  (): TestFunction<string | null | undefined> =>
  (phoneNumber: string | null | undefined): boolean => {
    if (phoneNumber === '' || phoneNumber == null) {
      return true;
    }

    if (phoneNumber.match(/[0-9-]+/)) {
      const len = phoneNumber.replace(/-/g, '').length;
      if (10 <= len && len <= 11) {
        return true;
      }
    }

    return false;
  };
