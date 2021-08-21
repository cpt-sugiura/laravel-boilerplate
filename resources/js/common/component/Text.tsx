import TextField from '@material-ui/core/TextField';
import React from 'react';
import { TextFieldProps } from '@material-ui/core/TextField/TextField';

type TextProps = {
  label: string;
  text: string | number;
  className?: string;
  TextFieldProps?: TextFieldProps;
};
export const Text: React.FC<TextProps> = (props) => {
  return (
    <TextField
      label={props.label}
      disabled
      className={`as-text ${props.className}`}
      value={props.text}
      {...props.TextFieldProps}
    />
  );
};

export const AppText = Text; // alias
