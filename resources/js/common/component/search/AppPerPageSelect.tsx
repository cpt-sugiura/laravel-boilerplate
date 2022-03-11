import FormControl, { FormControlProps } from '@mui/material/FormControl';
import Select, { SelectProps } from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import React, { useEffect, useState } from 'react';
import { useTrans } from '@/lang/useLangMsg';
import { arrUniq } from '@/common/helper';
import './AppPerPageSelect.scss';

type AppPerPageSelectProps = {
  label?: string;
  name?: string;
  perPage: number;
  onChangePerPage: (newPerPage: number) => void;
  perPageOptions?: number[];
  optionFormatter?: (perPage: number) => string;
  FormControlProps?: FormControlProps;
};
const DefaultPerPageOptions: AppPerPageSelectProps['perPageOptions'] = [30, 50, 100];
const DefaultPerPage: AppPerPageSelectProps['perPage'] = 30;

export const AppPerPageSelect: React.FC<AppPerPageSelectProps> = (props) => {
  const t = useTrans('search.pagination.');
  const name = props.name || 'perPage';
  const label = props.label || t('perPage');
  const perPageOptions =
    Array.isArray(props.perPageOptions) && props.perPageOptions.length > 0
      ? props.perPageOptions
      : arrUniq(DefaultPerPageOptions.concat([+props.perPage])).sort((a, b) => a - b);
  const formatter = props.optionFormatter || ((p: number) => `${p}${t('tail')}`);
  const [perPage, setPerPage] = useState(props.perPage || DefaultPerPage);

  useEffect(() => setPerPage(props.perPage || DefaultPerPage), [props.perPage]);

  const handleOnChange: SelectProps['onChange'] = (e) => {
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    const newPerPage = +e.target.value;
    setPerPage(newPerPage);
    props.onChangePerPage(newPerPage);
  };

  return (
    <div className="app-per-page-select">
      <span>{label}</span>
      <FormControl {...props.FormControlProps}>
        <Select
          name={name}
          value={perPage}
          onChange={handleOnChange}
          SelectDisplayProps={{
            style: {
              fontSize: '14px',
              padding: '5px 22px 5px 7px',
            },
          }}
        >
          {perPageOptions.map((option) => (
            <MenuItem key={option} value={option}>
              {formatter(option)}
            </MenuItem>
          ))}
        </Select>
      </FormControl>
    </div>
  );
};
