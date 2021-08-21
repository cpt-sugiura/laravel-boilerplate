import FormControl, { FormControlProps } from '@material-ui/core/FormControl';
import React from 'react';
import InputLabel from '@material-ui/core/InputLabel';
import MenuItem from '@material-ui/core/MenuItem';
import Select, { SelectProps } from '@material-ui/core/Select';
import FormHelperText from '@material-ui/core/FormHelperText';
import { useTrans } from '@/lang/useLangMsg';
import { SelectOption } from '@/common/const/System';

export const AppSelect = React.memo(AppSelectComponent);

export type AppSelectProps = {
  selectProps?: SelectProps;
  formControlProps?: FormControlProps;
  className?: string;
  label: string;
  name: string;
  value?: string | number;
  options: SelectOption[];
  onChange?: SelectProps['onChange'];
  helperText?: string;
  withoutNonSelect?: boolean;
};
/**
 * Select要素
 * @param props
 * @constructor
 */
function AppSelectComponent(props: AppSelectProps): JSX.Element {
  const t = useTrans();
  // 渡された value を型変換するので変数に移動
  let propsValue: string | number | undefined = props.value;
  // 型が統一されているなら引数も型合わせ
  if (propsValue !== '' && propsValue !== undefined && propsValue !== null) {
    if (props.options.filter((o) => typeof o.value === 'number').length === props.options.length) {
      propsValue = +propsValue;
    } else if (props.options.filter((o) => typeof o.value === 'string').length === props.options.length) {
      propsValue = String(propsValue);
    }
  }
  const notSelected = propsValue == undefined || !props.options.map((o) => o.value).includes(propsValue);

  return (
    <FormControl className={props.className} {...props.formControlProps}>
      <InputLabel>{`${props.selectProps?.required ? t('form.labelDeco.required') : ''}${props.label}`}</InputLabel>
      <Select
        value={notSelected ? '' : propsValue}
        onChange={props.onChange}
        label={`${props.selectProps?.required ? t('form.labelDeco.required') : ''}${props.label}`}
        name={props.name}
        {...props.selectProps}
      >
        {!props.withoutNonSelect && (
          <MenuItem value="">
            <em>{t('app.undefined.select')}</em>
          </MenuItem>
        )}
        {props.options.map((option) => (
          <MenuItem key={option.value} value={option.value}>
            {option.label}
          </MenuItem>
        ))}
      </Select>
      <FormHelperText>{props.helperText}</FormHelperText>
    </FormControl>
  );
}
