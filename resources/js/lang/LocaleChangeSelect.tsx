import React from 'react';
import { isLocale, localeOptions, useLangLocaleContext } from '@/lang/messageLoader';
import './LocaleChangeSelect.scss';
import { AppSelect, AppSelectProps } from '@/common/component/form/AppSelect';
import {AxiosInstance} from "axios";

type LocaleChangerProps = {
  label?: string;
  axiosInstance: AxiosInstance
};
export const LocaleChangeSelect: React.FC<LocaleChangerProps> = ({ label, axiosInstance }) => {
  const { langLocale, setLangLocale } = useLangLocaleContext();
  const handleChange: AppSelectProps['onChange'] = (e) => {
    if (isLocale(e.target.value)) {
      setLangLocale(e.target.value);
      axiosInstance.get(`/lang/${e.target.value}`, {
        withCredentials: true,
      });
    }
  };

  return (
    <AppSelect
      withoutNonSelect
      className={`local-change ${label ? '' : 'without-space'}`}
      selectProps={{ style: { background: 'rgb(232, 246, 238)' } }}
      label={label || ''}
      name={'langLocale'}
      options={localeOptions}
      value={langLocale}
      onChange={handleChange}
    />
  );
};
