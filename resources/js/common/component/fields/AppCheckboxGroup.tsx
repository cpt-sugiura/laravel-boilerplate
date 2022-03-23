import React, { useState } from 'react';
import FormControl from '@mui/material/FormControl';
import FormControlLabel from '@mui/material/FormControlLabel';
import Checkbox, { CheckboxProps } from '@mui/material/Checkbox';
import FormGroup from '@mui/material/FormGroup';
import FormLabel from '@mui/material/FormLabel';
import { SelectOption } from '@/common/const/System';
import { arrUniq } from '@/common/helperFunctions/arr';

export type AppCheckBoxGroupProps = {
  label: string;
  name: string;
  id?: string;
  value?: Array<string | number>;
  onChange?: (values: string[]) => void;
  options: SelectOption[];
  className?: string;
};

export const AppCheckboxGroup: React.FC<AppCheckBoxGroupProps> = (props) => {
  const [values, setValues] = useState<Array<string>>(props.value?.map((v) => `${v}`) || []);

  const handleChange: CheckboxProps['onChange'] = (e) => {
    let newValue;
    if (e.target.checked) {
      setValues((newValue = arrUniq<string>(values.concat(`${e.target.value}`))));
    } else {
      setValues((newValue = values.filter((v) => v !== `${e.target.value}`)));
    }
    props.onChange && props.onChange(newValue);
  };

  return (
    <FormControl className={props.className} id={props.id}>
      {values.length === 0 && <input type="hidden" name={`${props.name}[]`} />}
      <FormLabel component="legend">{props.label}</FormLabel>
      <FormGroup aria-label="position" row>
        {props.options.map((option, index) => {
          return (
            <FormControlLabel
              key={index}
              label={option.label}
              control={
                <React.Fragment>
                  <Checkbox
                    name={`${props.name}[]`}
                    value={`${option.value}`}
                    checked={values?.includes(`${option.value}`)}
                    onChange={handleChange}
                  />
                </React.Fragment>
              }
            />
          );
        })}
      </FormGroup>
    </FormControl>
  );
};
