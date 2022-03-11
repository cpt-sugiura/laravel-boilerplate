import Button, { ButtonProps } from '@mui/material/Button';
import ClearIcon from '@mui/icons-material/Clear';
import React from 'react';
import { useTrans } from '@/lang/useLangMsg';

export type ResetBtnProps = {
  onClick: ButtonProps['onClick'];
  className?: string;
};
export const ResetBtn: React.FC<ResetBtnProps> = ({ onClick, className }) => {
  const t = useTrans();
  // todo color
  return (
    <Button
      style={{ height: 'fit-content', width: 'fit-content' }}
      className={`search-clear control-btn ${className}`}
      onClick={onClick}
    >
      <ClearIcon />
      {t('search.searchBox.reset')}
    </Button>
  );
};
