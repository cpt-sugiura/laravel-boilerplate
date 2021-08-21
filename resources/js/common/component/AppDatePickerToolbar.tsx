import { ToolbarComponentProps } from '@material-ui/pickers/Picker/Picker';
import PickerToolbar from '@material-ui/pickers/_shared/PickerToolbar';
import React from 'react';
import { useTrans } from '@/lang/useLangMsg';
import AppDatePickerToolbarButton from '@/common/component/AppDatePickerToolbarButton';

export const AppDatePickerToolbar = (props: ToolbarComponentProps): JSX.Element => {
  const t = useTrans();
  const year = props.date?.getFullYear();
  return (
    <PickerToolbar isLandscape={false} style={{ height: 50 }}>
      <AppDatePickerToolbarButton
        variant={'h6'}
        onClick={() => props.setOpenView('year')}
        selected={props.openView === 'year'}
        label={t('datepicker.selectYear', [
          {
            searchValue: '{year}',
            replaceValue: year ? String(year) : '',
          },
        ])}
      />
    </PickerToolbar>
  );
};
