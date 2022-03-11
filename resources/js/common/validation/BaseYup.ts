import {LocaleJP} from './LocaleJP';
import {LocaleEn} from './LocaleEn';
import * as Yup from 'yup';
import {makePasswordRule} from './rules/password';
import {makeZipRule} from '@/common/validation/rules/zip';
import {makePhoneNumberRule} from '@/common/validation/rules/phoneNumber';
import {useLangLocaleContext} from '@/lang/messageLoader';
import {useTrans} from '@/lang/useLangMsg';
import {makeNullableMinRule} from '@/common/validation/rules/nullableMin';
import {makeNumericRule} from '@/common/validation/rules/numeric';
import {Message} from "yup/lib/types";

type ValidatorFn<T> = (message?: Message) => T;

type AppStringSchema<T extends string | null | undefined = string | undefined> =
  Omit<Yup.StringSchema, 'required' | 'nullable'> & {
    required(message?: Message): AppStringSchema<T>;
    nullable(isNullable?: true): AppStringSchema<T | null>;
    password: ValidatorFn<AppStringSchema<T>>;
    phoneNumber: ValidatorFn<AppStringSchema<T>>;
    zip: ValidatorFn<AppStringSchema<T>>;
    numeric: ValidatorFn<AppStringSchema<T>>;
    nullableMin(min: number): AppStringSchema<T>;
  };

type AppStringSchemaConstructor = () =>  AppStringSchema;
type ExtendedYupType = Omit<typeof Yup, 'string'> & {
  string: AppStringSchemaConstructor;
};
export const useYup = (): ExtendedYupType => {
  const {langLocale} = useLangLocaleContext();
  const t = useTrans('validation');
  const yupValidator = {
    ...Yup,
    string: Yup.string,
  };
  if (langLocale === 'ja') {
    yupValidator.setLocale(LocaleJP);
  } else if (langLocale === 'en') {
    yupValidator.setLocale(LocaleEn);
  }
  yupValidator.addMethod(yupValidator.string, 'password', function () {
    return this.test('password', t('password'), makePasswordRule());
  });
  yupValidator.addMethod(yupValidator.string, 'zip', function () {
    return this.test('zip', t('zip'), makeZipRule());
  });
  yupValidator.addMethod(yupValidator.string, 'phoneNumber', function () {
    return this.test('phoneNumber', t('phoneNumber'), makePhoneNumberRule());
  });
  yupValidator.addMethod(yupValidator.string, 'nullableMin', function (min: number) {
    return this.test('nullableMin', t('nullableMin'), makeNullableMinRule(min));
  });
  yupValidator.addMethod(yupValidator.string, 'numeric', function () {
    return this.test('numeric', t('numeric'), makeNumericRule());
  });

  // @ts-ignore
  return yupValidator;
};
