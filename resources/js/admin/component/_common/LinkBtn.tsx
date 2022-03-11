import Button, { ButtonProps } from '@mui/material/Button';
import React from 'react';
import { Link } from 'react-router-dom';

export type LinkBtnProps = {
  to: string;
  BtnProps?: ButtonProps;
};
export const LinkBtn: React.FC<LinkBtnProps> = ({ to, children }) => {
  return (
    <Button>
      <Link
        style={{
          color: 'white',
          width: '100%',
          height: '100%',
        }}
        to={to}
      >
        {children}
      </Link>
    </Button>
  );
};
