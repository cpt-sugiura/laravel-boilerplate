import * as React from 'react';
import clsx from 'clsx';
import Typography, { TypographyProps } from '@material-ui/core/Typography';
import { makeStyles, alpha } from '@material-ui/core/styles';

type ExtendMui<C, Removals extends keyof C = never> = Omit<C, 'classes' | 'theme' | Removals>;

export interface ToolbarTextProps extends ExtendMui<TypographyProps> {
  selected?: boolean;
  label: string;
}

export const useStyles = makeStyles(
  (theme) => {
    const textColor =
      theme.palette.type === 'light'
        ? theme.palette.primary.contrastText
        : theme.palette.getContrastText(theme.palette.background.default);

    return {
      toolbarTxt: {
        color: alpha(textColor, 0.54),
      },
      toolbarBtnSelected: {
        color: textColor,
      },
    };
  },
  { name: 'MuiPickersToolbarText' }
);

export const AppDatePickerToolbarText: React.FunctionComponent<ToolbarTextProps> = ({
  selected,
  label,
  className = null,
  ...other
}) => {
  const classes = useStyles();
  return (
    <Typography
      className={clsx(classes.toolbarTxt, className, {
        [classes.toolbarBtnSelected]: selected,
      })}
      {...other}
    >
      {label}
    </Typography>
  );
};
