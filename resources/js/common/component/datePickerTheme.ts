import createTheme, { Theme, ThemeOptions } from '@material-ui/core/styles/createTheme';
import { SimplePaletteColorOptions } from '@material-ui/core/styles/createPalette';
import { Color } from '@material-ui/core';

export const makeDatePickerTheme = (theme: ThemeOptions): Theme => {
  const primary = theme.palette?.primary as Partial<SimplePaletteColorOptions> & Partial<Color>;
  return createTheme({
    ...theme,
    overrides: {
      ...theme.overrides,
      MuiOutlinedInput: {
        ...theme.overrides?.MuiOutlinedInput,
        root: {
          ...theme.overrides?.MuiOutlinedInput?.root,
          width: '100%',
        },
      },
      MuiButton: {
        root: {
          width: 'auto',
          '&:not(:last-child)': {
            backgroundColor: 'rgba(0,0,0,0)',
            color: primary['900'],
            boxShadow: 'none',
          },
          '&:not(:last-child):hover': {
            backgroundColor: 'rgba(0,0,0,0.1)',
          },
        },
      },
    },
  });
};
