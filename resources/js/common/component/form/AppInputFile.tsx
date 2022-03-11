import OutlinedInput, { OutlinedInputProps } from '@mui/material/OutlinedInput';
import FormControl, { FormControlProps } from '@mui/material/FormControl';
import InputLabel, { InputLabelProps } from '@mui/material/InputLabel';
import FormHelperText from '@mui/material/FormHelperText';
import React from 'react';
import './AppInputFile.scss';
import { useTrans } from '@/lang/useLangMsg';
import { InputBaseProps } from '@mui/material/InputBase';

export type AppInputFileProps = Omit<InputBaseProps, 'onChange'> & {
  errorMessage?: string;
  label: string;
  onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void;
  formControlProps?: FormControlProps;
  inputLabelProps?: InputLabelProps;
  outlinedInputProps?: OutlinedInputProps;
  inputFileProps?: React.DetailedHTMLProps<React.InputHTMLAttributes<HTMLInputElement>, HTMLInputElement>;
};

export const AppInputFile = (props: AppInputFileProps): JSX.Element => {
  const [fileName, setFileName] = React.useState('');
  const handleChange: React.InputHTMLAttributes<HTMLInputElement>['onChange'] = (e) => {
    setFileName(e.target.value);
    props.onChange && props.onChange(e);
  };
  const t = useTrans();
  const label = `${props.required ? t('form.labelDeco.required') : ''}${props.label}`;
  // デザインの都合で用意
  const labelFull = `${props.required ? t('form.labelDeco.required') : ''}${props.label}${props.required ? ' *' : ''}`;

  return (
    <React.Fragment>
      <FormControl
        {...props.formControlProps}
        className={props.name}
        required={props.required}
        error={props.error}
        style={{
          ...props.style,
          position: 'relative',
        }}
        variant={'outlined'}
      >
        <input
          ref={props.inputRef}
          {...props.inputFileProps}
          style={{
            position: 'absolute',
            height: '100%',
            width: '100%',
            opacity: 0,
            zIndex: 1000,
          }}
          type={'file'}
          name={props.name}
          onChange={handleChange}
        />
        <InputLabel {...props.inputLabelProps} required={props.required} variant={'outlined'} error={props.error}>
          {label}
        </InputLabel>
        <OutlinedInput
          {...props.outlinedInputProps}
          required={props.required}
          type={'text'}
          label={labelFull}
          disabled={true}
          value={fileName.replace(/^.*[\\/]/, '')}
          error={props.error}
        />
        <FormHelperText>{props.errorMessage || ''}</FormHelperText>
      </FormControl>
    </React.Fragment>
  );
};
