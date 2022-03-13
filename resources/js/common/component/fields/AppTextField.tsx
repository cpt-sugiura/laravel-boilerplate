import TextField, { TextFieldProps } from '@mui/material/TextField';
import React from 'react';

export const AppTextField: React.FC<TextFieldProps> = (props) => {
  return (
    <TextField
      InputLabelProps={{ shrink: true }}
      className={`${props.name || ''} ${props.className || ''}`}
      {...props}
    />
  );
};
