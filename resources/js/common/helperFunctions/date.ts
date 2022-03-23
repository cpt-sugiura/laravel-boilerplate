/**
 * @param year
 * @param month 1-12
 */
export const getMonthCalendar = (year: number, month: number): Date[][] => {
  const lastDate = new Date(year, month, 0).getDate(); // 指定した年月の末日
  const calendar: Date[][] = []; // カレンダーの情報を格納
  let row = 0;
  for (let i = 1; i <= lastDate; i++) {
    const d = new Date(year, month - 1, i);
    if (calendar[row] === undefined) {
      calendar[row] = [];
    }
    calendar[row].push(d);
    if (d.getDay() === 6) {
      row++;
    }
  }
  return calendar;
};

export const getFirstDateInMonth = (d: Date) => {
  return new Date(d.getFullYear(), d.getMonth(), 1);
};
export const getLastDateInMonth = (d: Date) => {
  return new Date(d.getFullYear(), d.getMonth() + 1, 0);
};
