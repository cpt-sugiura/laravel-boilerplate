import React from 'react';
import { RowBox } from '../RowBox';
import TextField, { TextFieldProps } from '@material-ui/core/TextField';
import makeStyles from '@material-ui/core/styles/makeStyles';
import { BoxProps } from '@material-ui/core/Box';

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

const useStyles = makeStyles({
  'wavy-line': {
    marginLeft: '1%',
    marginRight: '1%',
    alignSelf: 'center',
  },
});
export const AppTextFieldRange: React.FC<AppTextFieldRangeProps> = (props) => {
  const styles = useStyles();
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
    <RowBox {...props.RootBoxProps}>
      <TextField
        {...props.StartTextFieldProps}
        label={props.startLabel}
        name={props.startName}
        helperText={props.startErrorMsg || ''}
        error={!!props.startErrorMsg}
        value={props.startValue}
        onChange={handleChangeStart}
      />
      <span className={styles['wavy-line']}>〜</span>
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
