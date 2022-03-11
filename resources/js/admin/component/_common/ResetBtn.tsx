import Button, { ButtonProps } from '@mui/material/Button';
import ClearIcon from '@mui/icons-material/Clear';
import React from 'react';

export type ResetBtnProps = {
  onClick: ButtonProps['onClick'];
  className?: string;
};
export const ResetBtn: React.FC<ResetBtnProps> = ({ onClick, className }) => {
  return (
    <Button
      style={{ height: 'fit-content' }}
      className={`search-clear control-btn back-btn ${className}`}
      onClick={onClick}
    >
      <ClearIcon />
      リセット
    </Button>
  );
};
