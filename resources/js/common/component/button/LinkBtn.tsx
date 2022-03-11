import Button, { ButtonProps } from '@mui/material/Button';
import React from 'react';
import { Link } from 'react-router-dom';

export type LinkBtnProps = {
  to: string;
  BtnProps?: ButtonProps;
  className?: string;
};
export const LinkBtn: React.FC<LinkBtnProps> = ({ to, children, BtnProps, className }) => {
  return (
    <Button {...BtnProps} className={className}>
      <Link
        style={{
          color: 'white',
          width: '100%',
          height: '100%',
          textDecorationLine: 'none',
          display: 'inline-flex',
          alignItems: 'center',
          justifyContent: 'center',
          gap: '3px',
        }}
        to={to}
      >
        {children}
      </Link>
    </Button>
  );
};
