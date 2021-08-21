import { SelectOption } from '@/common/const/System';

const SQL_DAY_OF_WEEK = {
  SUNDAY: 1,
  MONDAY: 2,
  TUESDAY: 3,
  WEDNESDAY: 4,
  THURSDAY: 5,
  FRIDAY: 6,
  SATURDAY: 7,
};

export const useSqlDayOfWeekOptions = (selectedValue?: unknown): SelectOption[] => {
  if (typeof selectedValue === 'string' && selectedValue.match(/^[1-7]$/)) {
    selectedValue = Number(selectedValue);
  }
  return [
    { label: '月曜日', value: SQL_DAY_OF_WEEK.MONDAY, selected: selectedValue === SQL_DAY_OF_WEEK.MONDAY },
    { label: '火曜日', value: SQL_DAY_OF_WEEK.TUESDAY, selected: selectedValue === SQL_DAY_OF_WEEK.TUESDAY },
    { label: '水曜日', value: SQL_DAY_OF_WEEK.WEDNESDAY, selected: selectedValue === SQL_DAY_OF_WEEK.WEDNESDAY },
    { label: '木曜日', value: SQL_DAY_OF_WEEK.THURSDAY, selected: selectedValue === SQL_DAY_OF_WEEK.THURSDAY },
    { label: '金曜日', value: SQL_DAY_OF_WEEK.FRIDAY, selected: selectedValue === SQL_DAY_OF_WEEK.FRIDAY },
    { label: '土曜日', value: SQL_DAY_OF_WEEK.SATURDAY, selected: selectedValue === SQL_DAY_OF_WEEK.SATURDAY },
    { label: '日曜日', value: SQL_DAY_OF_WEEK.SUNDAY, selected: selectedValue === SQL_DAY_OF_WEEK.SUNDAY },
  ];
};
