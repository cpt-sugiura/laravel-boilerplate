import Button, { ButtonProps } from '@material-ui/core/Button';
import ClearIcon from '@material-ui/icons/Clear';
import React from 'react';
import { useTrans } from '@/lang/useLangMsg';

export type ResetBtnProps = {
  onClick: ButtonProps['onClick'];
  className?: string;
};
export const ResetBtn: React.FC<ResetBtnProps> = ({ onClick, className }) => {
  const t = useTrans();
  return (
    <Button
      color={'default'}
      style={{ height: 'fit-content', width: 'fit-content' }}
      className={`search-clear control-btn ${className}`}
      onClick={onClick}
    >
      <ClearIcon />
      {t('search.searchBox.reset')}
    </Button>
  );
};
