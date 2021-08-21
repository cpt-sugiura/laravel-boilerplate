import DateFnsUtils from '@date-io/date-fns';
import format from 'date-fns/format';

/**
 * DatePickerの依存関数の日本語化
 */
export class JaUtils extends DateFnsUtils {
  /**
   * カレンダーヘッダ
   * @param date
   */
  getCalendarHeaderText(date: Date): string {
    return format(date, 'yyyy年MMM', { locale: this.locale });
  }
  /**
   * DatePicker ヘッダ
   * @param date
   */
  getDatePickerHeaderText(date: number | Date): string {
    return format(date, 'MMMd日', { locale: this.locale });
  }
  getDayText(date: number | Date): string {
    return format(date, 'd', { locale: this.locale });
  }
}
