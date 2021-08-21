import DateFnsUtils from '@date-io/date-fns';
import format from 'date-fns/format';

/**
 * DatePickerの依存関数の英語化
 */
export class EnUtils extends DateFnsUtils {
  /**
   * カレンダーヘッダ
   * @param date
   */
  getCalendarHeaderText(date: Date): string {
    return format(date, 'yyyy MMM', { locale: this.locale });
  }
  /**
   * DatePicker ヘッダ
   * @param date
   */
  getDatePickerHeaderText(date: number | Date): string {
    return format(date, 'MMM d', { locale: this.locale });
  }
}
