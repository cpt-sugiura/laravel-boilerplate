import React, { useEffect, useState } from 'react';
import { DatePickerProps, KeyboardDatePicker, KeyboardDateTimePickerProps } from '@material-ui/pickers';
import { MaterialUiPickersDate } from '@material-ui/pickers/typings/date';
import format from 'date-fns/format';
import { RowBox } from '@/common/component/RowBox';
import { MuiThemeProvider, useTheme } from '@material-ui/core';
import makeStyles from '@material-ui/core/styles/makeStyles';
import { BoxProps } from '@material-ui/core/Box';
import { makeDatePickerTheme } from '@/common/component/datePickerTheme';
import { AppDatePickerToolbar } from '@/common/component/AppDatePickerToolbar';

type AppDateRangeInputProps = {
  startLabel: string;
  startName: string;
  emitStartDate: (date: { target: { name?: string; value: string | number } }) => void;
  startErrorMsg?: string;
  endLabel: string;
  endName: string;
  emitEndDate: (date: { target: { name?: string; value: string | number } }) => void;
  endErrorMsg?: string;
  RootBoxProps?: BoxProps;

  resetTriggerVal?: string | number;
};

const useStyles = makeStyles({
  'wavy-line': {
    marginLeft: '1%',
    marginRight: '1%',
    alignSelf: 'center',
  },
});
export const AppDateRangeInput: React.FC<AppDateRangeInputProps> = (props) => {
  const styles = useStyles();
  const makeLabel: KeyboardDateTimePickerProps['labelFunc'] = (date: MaterialUiPickersDate) =>
    date === null ? '' : format(new Date(date), 'yyyy年MM月dd日');

  const [startValue, setStartValue] = useState<MaterialUiPickersDate | null>(null);
  const handleChangeStart: DatePickerProps['onChange'] = (date) => {
    setStartValue(date);
    props.emitStartDate({
      target: {
        name: props.startName,
        value: date ? format(date, 'yyyy-MM-dd HH:mm:ss') : '',
      },
    });
  };

  const [endValue, setEndValue] = useState<MaterialUiPickersDate | null>(null);
  const handleChangeEnd: DatePickerProps['onChange'] = (date) => {
    setEndValue(date);
    props.emitEndDate({
      target: {
        name: props.endName,
        value: date ? format(date, 'yyyy-MM-dd') : '',
      },
    });
  };

  useEffect(() => {
    setStartValue(null);
    setEndValue(null);
  }, [props.resetTriggerVal]);

  const theme = useTheme();
  return (
    <MuiThemeProvider theme={makeDatePickerTheme(theme)}>
      <RowBox {...props.RootBoxProps}>
        <KeyboardDatePicker
          format="fullDate"
          okLabel="決定"
          cancelLabel="戻る"
          clearLabel="入力を削除"
          clearable
          labelFunc={makeLabel}
          ToolbarComponent={AppDatePickerToolbar}
          autoOk
          invalidDateMessage="正しい形式で入力してください。"
          label={props.startLabel}
          name={props.startName}
          helperText={props.startErrorMsg || ''}
          error={!!props.startErrorMsg}
          value={startValue}
          onChange={handleChangeStart}
        />
        <span className={styles['wavy-line']}>〜</span>
        <KeyboardDatePicker
          format="fullDate"
          okLabel="決定"
          cancelLabel="戻る"
          clearLabel="入力を削除"
          clearable
          labelFunc={makeLabel}
          ToolbarComponent={AppDatePickerToolbar}
          autoOk
          invalidDateMessage="正しい形式で入力してください。"
          label={props.endLabel}
          name={props.endName}
          helperText={props.endErrorMsg || ''}
          error={!!props.endErrorMsg}
          value={endValue}
          onChange={handleChangeEnd}
        />
      </RowBox>
    </MuiThemeProvider>
  );
};
