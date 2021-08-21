import React, { CSSProperties, useState } from 'react';
import FormControl, { FormControlProps } from '@material-ui/core/FormControl';
import Grid, { GridProps } from '@material-ui/core/Grid';
import Switch from '@material-ui/core/Switch';
import FormHelperText from '@material-ui/core/FormHelperText';
import { SwitchProps } from '@material-ui/core/Switch/Switch';
export type AppToggleProps = {
  FormControlProps?: FormControlProps;
  SwitchProps?: SwitchProps;
  className?: string;
  label: string;
  name: string;
  checked?: boolean;
  onChange?: SwitchProps['onChange'];
  falseSideLabel: string;
  trueSideLabel: string;
  helperText?: string;
  style?: CSSProperties;
  inputRef?: React.Ref<HTMLInputElement>;
  GridContainerProps?: GridProps;
};
export const AppToggle: React.FC<AppToggleProps> = (props) => {
  const [checked, setChecked] = useState<0 | 1>(props.checked ? 1 : 0);
  const handleChange: SwitchProps['onChange'] = (e, checkedInEvent) => {
    setChecked(checkedInEvent ? 1 : 0);
    props.onChange && props.onChange(e, checkedInEvent);
  };
  return (
    <FormControl
      className={props.className}
      style={{ minWidth: '8em', margin: '.5em', ...props.style }}
      {...props.FormControlProps}
    >
      <input type="hidden" name={props.name} value={checked} ref={props.inputRef} />
      <label
        style={{
          marginBottom: 0,
          fontSize: '.75em',
          transform: 'translateY(-.75em)',
          paddingLeft: '1em',
        }}
      >
        {props.label}
      </label>
      <Grid
        component="label"
        container
        alignItems="center"
        spacing={1}
        style={{
          transform: 'translateY(-.5em)',
          paddingLeft: '.5em',
          ...props?.GridContainerProps?.style,
        }}
      >
        <Grid item>{props.falseSideLabel}</Grid>
        <Grid item>
          <Switch checked={props.checked} onChange={handleChange} name={props.name} {...props.SwitchProps} />
        </Grid>
        <Grid item>{props.trueSideLabel}</Grid>
      </Grid>
      <FormHelperText>{props.helperText}</FormHelperText>
    </FormControl>
  );
};
