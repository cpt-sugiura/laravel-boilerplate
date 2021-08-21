import Button, { ButtonProps } from '@material-ui/core/Button';
import ClearIcon from '@material-ui/icons/Clear';
import React from 'react';

export type ResetBtnProps = {
  onClick: ButtonProps['onClick'];
  className?: string;
};
export const ResetBtn: React.FC<ResetBtnProps> = ({ onClick, className }) => {
  return (
    <Button
      color={'default'}
      style={{ height: 'fit-content' }}
      className={`search-clear control-btn ${className}`}
      onClick={onClick}
    >
      <ClearIcon />
      リセット
    </Button>
  );
};
