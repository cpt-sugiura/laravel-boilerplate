import * as React from 'react';
import * as PropTypes from 'prop-types';
import clsx from 'clsx';
import Button, { ButtonProps } from '@material-ui/core/Button';
import { TypographyProps } from '@material-ui/core/Typography';
import { createStyles, withStyles, WithStyles } from '@material-ui/core/styles';
import { AppDatePickerToolbarText } from '@/common/component/AppDatePickerToolbarText';

type ExtendMui<C, Removals extends keyof C = never> = Omit<C, 'classes' | 'theme' | Removals>;

export interface ToolbarButtonProps extends ExtendMui<ButtonProps, 'variant'>, WithStyles<typeof styles> {
  variant: TypographyProps['variant'];
  selected: boolean;
  label: string;
  align?: TypographyProps['align'];
  typographyClassName?: string;
}

const AppDatePickerToolbarButton: React.FunctionComponent<ToolbarButtonProps> = ({
  classes,
  className = null,
  label,
  selected,
  variant,
  align,
  typographyClassName,
  ...other
}) => {
  return (
    <Button variant="text" className={clsx(classes.toolbarBtn, className)} {...other}>
      <AppDatePickerToolbarText
        align={align}
        className={typographyClassName}
        variant={variant}
        label={label}
        selected={selected}
      />
    </Button>
  );
};

/* eslint-disable-next-line @typescript-eslint/no-explicit-any */
(AppDatePickerToolbarButton as any).propTypes = {
  selected: PropTypes.bool.isRequired,
  label: PropTypes.string.isRequired,
  classes: PropTypes.any.isRequired,
  className: PropTypes.string,
  innerRef: PropTypes.any,
};

AppDatePickerToolbarButton.defaultProps = {
  className: '',
};

export const styles = createStyles({
  toolbarBtn: {
    padding: 0,
    minWidth: '16px',
    textTransform: 'none',
  },
});

export default withStyles(styles, { name: 'AppDatePickerToolbarButton' })(AppDatePickerToolbarButton);
