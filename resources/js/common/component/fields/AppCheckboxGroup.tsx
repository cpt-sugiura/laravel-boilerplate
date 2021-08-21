import React, { useState } from 'react';
import FormControl from '@material-ui/core/FormControl';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import Checkbox, { CheckboxProps } from '@material-ui/core/Checkbox';
import FormGroup from '@material-ui/core/FormGroup';
import FormLabel from '@material-ui/core/FormLabel';
import { arrUniq } from '@/common/helper';
import { SelectOption } from '@/common/const/System';

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
