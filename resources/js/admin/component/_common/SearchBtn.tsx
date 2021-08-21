import Button, { ButtonProps } from '@material-ui/core/Button';
import SearchIcon from '@material-ui/icons/Search';
import React from 'react';

export type SearchBtnProps = {
  onClick: ButtonProps['onClick'];
  className?: string;
};
export const SearchBtn: React.FC<SearchBtnProps> = ({ onClick, className }) => {
  return (
    <Button
      style={{ height: 'fit-content' }}
      color={'primary'}
      className={`submit control-btn ${className}`}
      onClick={onClick}
    >
      <SearchIcon />
      検索する
    </Button>
  );
};
