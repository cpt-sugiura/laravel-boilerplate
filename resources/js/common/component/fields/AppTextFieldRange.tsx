import React from 'react';
import { RowBox } from '../RowBox';
import TextField, { TextFieldProps } from '@mui/material/TextField';
import { BoxProps } from '@mui/material/Box';
import './AppTextFieldRange.scss'

type AppTextFieldRangeProps = {
  startLabel: string;
  startName: string;
  emitStartValue: (event: { target: { name?: string; value: string | number } }) => void;
  startValue?: string | number;
  startErrorMsg?: string;
  endLabel: string;
  endName: string;
  emitEndValue: (event: { target: { name?: string; value: string | number } }) => void;
  endValue?: string | number;
  endErrorMsg?: string;
  RootBoxProps?: BoxProps;
  StartTextFieldProps?: TextFieldProps;
  EndTextFieldProps?: TextFieldProps;
};

export const AppTextFieldRange: React.FC<AppTextFieldRangeProps> = (props) => {
  const handleChangeStart: TextFieldProps['onChange'] = (e) => {
    props.emitStartValue({
      target: {
        name: props.startName,
        value: e.target.value,
      },
    });
  };

  const handleChangeEnd: TextFieldProps['onChange'] = (e) => {
    props.emitEndValue({
      target: {
        name: props.endName,
        value: e.target.value,
      },
    });
  };

  return (
    <RowBox {...props.RootBoxProps} className={`app-text-field-range ${props.RootBoxProps?.className}`}>
      <TextField
        {...props.StartTextFieldProps}
        label={props.startLabel}
        name={props.startName}
        helperText={props.startErrorMsg || ''}
        error={!!props.startErrorMsg}
        value={props.startValue}
        onChange={handleChangeStart}
      />
      <span className={'wavy-line'}>〜</span>
      <TextField
        {...props.EndTextFieldProps}
        label={props.endLabel}
        name={props.endName}
        helperText={props.endErrorMsg || ''}
        error={!!props.endErrorMsg}
        value={props.endValue}
        onChange={handleChangeEnd}
      />
    </RowBox>
  );
};
