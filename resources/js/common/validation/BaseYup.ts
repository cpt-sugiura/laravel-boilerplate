import { LocaleJP } from './LocaleJP';
import { LocaleEn } from './LocaleEn';
import * as Yup from 'yup';
import { Schema, StringSchemaConstructor, TestFunction, TestOptionsMessage } from 'yup';
import { makePasswordRule } from './rules/password';
import { makeZipRule } from '@/common/validation/rules/zip';
import { makePhoneNumberRule } from '@/common/validation/rules/phoneNumber';
import { useLangLocaleContext } from '@/lang/messageLoader';
import { useTrans } from '@/lang/useLangMsg';
import { makeNullableMinRule } from '@/common/validation/rules/nullableMin';
import { makeNumericRule } from '@/common/validation/rules/numeric';

type ValidatorFn<T> = (
  message?: string | ((params: object & Partial<Yup.TestMessageParams>) => string) | undefined
) => T;

interface AppSchema<T> extends Schema<T> {
  isRequired: ValidatorFn<this>;
  test(name: string, message: TestOptionsMessage, test: TestFunction): this;
}

type AppStringSchema<T extends string | null | undefined = string | undefined> = AppSchema<T> &
  Omit<Yup.StringSchema, 'required' | 'nullable'> & {
    required(message?: TestOptionsMessage): AppStringSchema<T>;
    nullable(isNullable?: true): AppStringSchema<T | null>;
    password: ValidatorFn<AppStringSchema<T>>;
    phoneNumber: ValidatorFn<AppStringSchema<T>>;
    zip: ValidatorFn<AppStringSchema<T>>;
    numeric: ValidatorFn<AppStringSchema<T>>;
    nullableMin(min: number): AppStringSchema<T>;
  };

interface AppStringSchemaConstructor extends StringSchemaConstructor {
  (): AppStringSchema;
  new (): AppStringSchema;
}

type ExtendedYupType = Omit<typeof Yup, 'string'> & {
  string: AppStringSchemaConstructor;
};

export const useYup = (): ExtendedYupType => {
  const { langLocale } = useLangLocaleContext();
  const t = useTrans('validation');
  const yupValidator: ExtendedYupType = {
    ...Yup,
    string: Yup.string as AppStringSchemaConstructor,
  };
  if (langLocale === 'ja') {
    yupValidator.setLocale(LocaleJP);
  } else if (langLocale === 'en') {
    yupValidator.setLocale(LocaleEn);
  }
  // todo types
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  yupValidator.addMethod<AppStringSchema>(yupValidator.string, 'password', function () {
    return this.test('password', t('password'), makePasswordRule());
  });
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  yupValidator.addMethod<AppStringSchema>(yupValidator.string, 'zip', function () {
    return this.test('zip', t('zip'), makeZipRule());
  });
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  yupValidator.addMethod<AppStringSchema>(yupValidator.string, 'phoneNumber', function () {
    return this.test('phoneNumber', t('phoneNumber'), makePhoneNumberRule());
  });
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  yupValidator.addMethod<AppStringSchema>(yupValidator.string, 'nullableMin', function (min: number) {
    return this.test('nullableMin', t('nullableMin'), makeNullableMinRule(min));
  });
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  yupValidator.addMethod<AppStringSchema>(yupValidator.string, 'numeric', function () {
    return this.test('numeric', t('numeric'), makeNumericRule());
  });

  return yupValidator;
};
