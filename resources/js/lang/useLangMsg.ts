import { MessageDescriptor, useIntl } from 'react-intl';
import React from 'react';
import { AppLocale } from '@/lang/messageLoader';

type MessageFormatPrimitiveValue = string | number | boolean | null | undefined;

type MessageFormatValues = Record<string, MessageFormatPrimitiveValue | React.ReactElement>;

type MessageFactory = (message: MessageDescriptor, values?: MessageFormatValues) => React.ReactNode;
type MessageEasyFactory = (id: string | number, replaces?: Replacer[]) => string;
/**
 * react-intl の文字列出力メソッドのラッパー
 */
export const useIntlFormatMsg = (): MessageFactory => {
  const intl = useIntl();

  return (message, values) => intl.formatMessage(message, values);
};
type Replacer = {
  searchValue: string | RegExp;
  replaceValue: string;
};
/**
 * react-intl の文字列出力メソッドのラッパー。簡易版
 */
export const useTrans = (keyPrefix?: string, keyPrefixLastIsNotDot?: boolean): MessageEasyFactory => {
  const intl = useIntl();
  if (typeof keyPrefix === 'string' && !keyPrefixLastIsNotDot && keyPrefix[keyPrefix.length - 1] !== '.') {
    keyPrefix = `${keyPrefix}.`;
  }

  return (id, replaces?: Replacer[]) => {
    id = `${keyPrefix || ''}${id}`;
    let msg = intl.formatMessage({ id });
    replaces?.forEach((r) => (msg = msg.replace(r.searchValue, r.replaceValue)));

    return msg;
  };
};
import format from 'date-fns/format';
import jaLocale from 'date-fns/locale/ja';
import enLocale from 'date-fns/locale/en-US';
export const useTransDate = (locale: AppLocale): ((date: string | Date) => string) => {
  return (date: string | Date): string => {
    const d = new Date(date);

    if (locale === 'en') {
      return format(d, 'MMM d', { locale: enLocale });
    }
    return format(d, 'MM月d日（E）', { locale: jaLocale });
  };
};
