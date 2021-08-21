import TextField, { TextFieldProps } from '@material-ui/core/TextField';
import React, { useState } from 'react';
import { useFormContext } from 'react-hook-form';
import { useTrans } from '@/lang/useLangMsg';
import { InputBaseProps } from '@material-ui/core/InputBase';
import InputAdornment from '@material-ui/core/InputAdornment';
import IconButton from '@material-ui/core/IconButton';
import { Visibility, VisibilityOff } from '@material-ui/icons';

type AppFormTextFieldProps = TextFieldProps & {
  name: string;
  cantPaste?: boolean;
};
const AppFormTextField: React.FC<AppFormTextFieldProps> = (props) => {
  const {
    formState: { errors },
    setValue,
    register,
  } = useFormContext();
  const { cantPaste, ...textFieldProps } = props;
  const handleChange: InputBaseProps['onChange'] = (e) => {
    register(props.name).onChange(e);
    setValue(props.name, e.target.value);
  };
  const className = [props.name, props?.className].filter((v) => !!v).join(' ');
  const handlePaste: InputBaseProps['onPaste'] = (e) => {
    if (cantPaste) {
      e.preventDefault();
      alert(t('form.cantPast.msg', [{ searchValue: '{label}', replaceValue: (props.label || '').toString() }]));
    }
  };
  const [showPassword, setShowPassword] = useState(false);
  const t = useTrans();
  if (props.type === 'password') {
    return (
      <TextField
        error={!!errors[props.name]}
        helperText={errors[props.name]?.message || props.helperText}
        {...textFieldProps}
        InputProps={{
          ...props.InputProps,
          ...register(props.name),
          onChange: handleChange,
          className: `${props.InputProps?.className} ${showPassword ? '' : 'mimic-password'}`,
          onPaste: handlePaste,
          endAdornment: (
            <InputAdornment position="end">
              <IconButton onClick={() => setShowPassword(!showPassword)}>
                {showPassword ? <Visibility /> : <VisibilityOff />}
              </IconButton>
            </InputAdornment>
          ),
        }}
        inputRef={register(props.name).ref}
        type="text"
        autoComplete="off"
        className={className}
        label={`${props.required ? t('form.labelDeco.required') : ''}${props.label}`}
      />
    );
  }

  return (
    <TextField
      error={!!errors[props.name]}
      helperText={errors[props.name]?.message || props.helperText}
      InputProps={{ ...register(props.name), onChange: handleChange }}
      inputRef={register(props.name).ref}
      {...textFieldProps}
      onPaste={handlePaste}
      className={className}
      label={`${props.required ? t('form.labelDeco.required') : ''}${props.label}`}
    />
  );
};

const memo = React.memo(AppFormTextField);
export { memo as AppFormTextField };
