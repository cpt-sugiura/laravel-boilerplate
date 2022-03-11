import ja from './messages/ja.yml';
import en from './messages/en.yml';
import React, { PropsWithChildren, useContext, useState } from 'react';
import { SelectOption } from '@/common/const/System';

export type AppLocale = 'ja' | 'en'; // 今後はパイプで en などが増える

export const isLocale = (str: unknown): str is AppLocale => typeof str === 'string' && ['ja', 'en'].includes(str);
const defaultLocale = ((): AppLocale => {
  const defaultLang = document.querySelector('html')?.lang;
  if (isLocale(defaultLang)) {
    return defaultLang;
  }
  return 'ja';
})();

export const localeOptions: SelectOption[] = [
  { label: '日本語', value: 'ja' },
  { label: 'English', value: 'en' },
];

export const messages: Record<AppLocale, Record<string, string>> = {
  ja,
  en,
};

type LangLocaleContextType = { langLocale: AppLocale; setLangLocale: (langLocale: AppLocale) => void };

export const LangLocaleContext = React.createContext<LangLocaleContextType>({
  langLocale: defaultLocale,
  // eslint-disable-next-line @typescript-eslint/no-empty-function,@typescript-eslint/no-unused-vars
  setLangLocale: (langLocale: AppLocale) => {},
});

export const useLangLocaleContext = (): LangLocaleContextType => useContext(LangLocaleContext);
export const LangLocaleProvider = LangLocaleContextComponent;

/**
 * LangLocaleを扱う
 * @constructor
 */
function LangLocaleContextComponent(props: PropsWithChildren<{}>): JSX.Element {
  const [langLocale, setLangLocale] = useState<AppLocale>(defaultLocale);
  const setLangLocaleWithSideEffect = (newLocale: AppLocale) => {
    setLangLocale(newLocale);
    const htmlEl = document.querySelector('html');
    if (htmlEl) {
      htmlEl.lang = newLocale;
    }
  };
  return (
    <LangLocaleContext.Provider value={{ langLocale, setLangLocale: setLangLocaleWithSideEffect }}>
      {props.children}
    </LangLocaleContext.Provider>
  );
}
