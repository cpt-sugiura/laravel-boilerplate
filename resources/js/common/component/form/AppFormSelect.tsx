import React, { useEffect, useState } from 'react';
import { SelectProps } from '@mui/material/Select';
import { AppSelect, AppSelectProps } from './AppSelect';
import { useFormContext } from 'react-hook-form';

type AppFormSelectProps = AppSelectProps;

const AppFormSelect: React.FC<AppFormSelectProps> = (props) => {
  const {
    formState: { errors },
    register,
    setValue,
    getValues,
  } = useFormContext();
  const [v, setV] = useState<number | string>('');

  const handleChange: SelectProps['onChange'] = (e) => {
    setValue(props.name, e.target.value);
    setV(e.target.value as string);
  };
  useEffect(() => {
    register(props.name);
    setV(getValues()[props.name]);
  }, [props.options, register]);

  return (
    <AppSelect
      onChange={handleChange}
      formControlProps={{ error: !!errors[props.name], ...props.formControlProps }}
      selectProps={{ error: !!errors[props.name], inputRef: register(props.name).ref, ...props.selectProps }}
      helperText={errors[props.name]?.message}
      value={v}
      {...props}
    />
  );
};
const memo = React.memo(AppFormSelect);
export { memo as AppFormSelect };
