import { TestFunction } from 'yup';

/** nullable可な min ルール を生成する */
export const makeNullableMinRule =
  (min: number): TestFunction<string | null | undefined> =>
  (value: string | null | undefined): boolean => {
    if (value === '' || value == null) {
      return true;
    }
    return value.length >= min;
  };
