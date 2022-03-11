import Button, { ButtonProps } from '@mui/material/Button';
import SearchIcon from '@mui/icons-material/Search';
import React from 'react';
import { useTrans } from '@/lang/useLangMsg';

export type SearchBtnProps = {
  onClick: ButtonProps['onClick'];
  className?: string;
};
export const SearchBtn: React.FC<SearchBtnProps> = ({ onClick, className }) => {
  const t = useTrans();
  return (
    <Button
      style={{ height: 'fit-content', width: 'fit-content' }}
      color={'primary'}
      className={`submit control-btn ${className}`}
      onClick={onClick}
    >
      <SearchIcon />
      {t('search.searchBox.run')}
    </Button>
  );
};
