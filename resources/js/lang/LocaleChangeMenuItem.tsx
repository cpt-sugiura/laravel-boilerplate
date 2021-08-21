import React from 'react';
import { isLocale, localeOptions, useLangLocaleContext } from '@/lang/messageLoader';
import './LocaleChangeSelect.scss';
import Popover from '@material-ui/core/Popover';
import MenuItem, { MenuItemProps } from '@material-ui/core/MenuItem';
import { useTrans } from '@/lang/useLangMsg';
import { TriangleIcon } from '@/common/icons/component/TriangleIcon';
import { useUserAxios } from '@/user/hook/API/useUserAxios';
import { SelectOption } from '@/common/const/System';

export const LocaleChangeMenuItem: React.FC = () => {
  const { setLangLocale, langLocale } = useLangLocaleContext();
  const { axiosInstance } = useUserAxios();
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
