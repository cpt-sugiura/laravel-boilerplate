import React from 'react';
import { isLocale, localeOptions, useLangLocaleContext } from '@/lang/messageLoader';
import './LocaleChangeSelect.scss';
import Popover from '@mui/material/Popover';
import MenuItem, { MenuItemProps } from '@mui/material/MenuItem';
import { useTrans } from '@/lang/useLangMsg';
import { TriangleIcon } from '@/common/icons/component/TriangleIcon';
import { SelectOption } from '@/common/const/System';
import {AxiosInstance} from "axios";

export const LocaleChangeMenuItem: React.FC<{axiosInstance: AxiosInstance}> = ({ axiosInstance }) => {
  const { setLangLocale, langLocale } = useLangLocaleContext();
  const changeLocale = (locale: SelectOption) => {
    if (isLocale(locale.value)) {
      setLangLocale(locale.value);
      axiosInstance.get(`/lang/${locale.value}`, {
        withCredentials: true,
      });
    }
  };
  const t = useTrans();
  const handleSelect = (option: SelectOption) => {
    changeLocale(option);
  };
  const [anchorEl, setAnchorEl] = React.useState<null | HTMLElement>(null);
  const handleOpen: MenuItemProps['onClick'] = (e) => setAnchorEl(e.currentTarget);
  const handleClose = () => setAnchorEl(null);
  return (
    <>
      <MenuItem onClick={handleOpen}>
        {`${t('app.lang')}: ${localeOptions.filter((opt) => opt.value === langLocale)[0].label}`}
        <TriangleIcon />
      </MenuItem>
      <Popover
        open={!!anchorEl}
        anchorEl={anchorEl}
        onClose={handleClose}
        anchorOrigin={{
          vertical: 'center',
          horizontal: 'right',
        }}
        transformOrigin={{
          vertical: 'top',
          horizontal: 'center',
        }}
      >
        {localeOptions.map((op) => (
          <MenuItem key={op.value} onClick={() => handleSelect(op)}>
            {op.label}
          </MenuItem>
        ))}
      </Popover>
    </>
  );
};
