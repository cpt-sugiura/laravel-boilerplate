import format from 'date-fns/format';
import ja from 'date-fns/locale/ja';
export const useDateTimeFormatters = (): {
  dateFormatter: (d: Date | string) => string;
  df: (d: Date | string) => string;
  dateMinuteFormatter: (d: Date | string) => string;
  dmf: (d: Date | string) => string;
  dateTimeFormatter: (d: Date | string) => string;
  dtf: (d: Date | string) => string;
} => {
  const dateFormatter = (d: Date | string): string => {
    if (typeof d === 'string') {
      d = new Date(d);
    }
    return format(d, 'yyyy年MM月d日(E)', { locale: ja });
  };
  const dateMinuteFormatter = (d: Date | string): string => {
    if (typeof d === 'string') {
      d = new Date(d);
    }
    return format(d, 'yyyy年MM月d日(E) HH:mm', { locale: ja });
  };
  const dateTimeFormatter = (d: Date | string): string => {
    if (typeof d === 'string') {
      d = new Date(d);
    }
    return format(d, 'yyyy年MM月d日(E) HH:mm:ss', { locale: ja });
  };
  return {
    dateFormatter,
    df: dateFormatter,
    dateMinuteFormatter,
    dmf: dateMinuteFormatter,
    dateTimeFormatter,
    dtf: dateTimeFormatter,
  };
};
