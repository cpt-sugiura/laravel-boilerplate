import Button, { ButtonProps } from '@mui/material/Button';
import React from 'react';

export type ControlBtnInRowProps = {
  BtnProps?: ButtonProps;
};
export const ControlBtnInTableRow: React.FC<ControlBtnInRowProps> = (props) => {
  return (
    <Button style={{ width: 'fit-content', ...props.BtnProps?.style }} {...props.BtnProps}>
      {props.children}
    </Button>
  );
};
