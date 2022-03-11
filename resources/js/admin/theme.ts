import {createTheme} from '@mui/material/styles';
import {
  PaletteOptions,
  SimplePaletteColorOptions
} from '@mui/material/styles/createPalette';
import {Color} from '@mui/material';

export const primaryThinColor = '';
export const primaryColor: Partial<SimplePaletteColorOptions> & Partial<Color> = {
  50: '#e8eaf6',
  100: '#c5cbe9',
  200: '#9fa8da',
  300: '#7985cb',
  400: '#5c6bc0',
  500: '#3f51b5',
  600: '#394aae',
  700: '#3140a5',
  800: '#29379d',
  900: '#1b278d',
  A100: '#c6cbff',
  A200: '#939dff',
  A400: '#606eff',
  A700: '#4757ff',
  contrastText: '#ffffff',
};
export const secondaryColor: SimplePaletteColorOptions = {
  light: '#8ecb6c',
  main: '#5db52d',
  dark: '#308d11',
  contrastText: '#fff',
};
export const errorColor: SimplePaletteColorOptions = {
  light: '#e57373',
  main: '#f44336',
  dark: '#d32f2f',
  contrastText: '#fff',
};
export const warningColor: SimplePaletteColorOptions = {
  light: '#ffb74d',
  main: '#ff9800',
  dark: '#f57c00',
  contrastText: 'rgba(0, 0, 0, 0.87)',
};
export const successColor: SimplePaletteColorOptions = {
  light: '#81c784',
  main: '#4caf50',
  dark: '#388e3c',
  contrastText: 'rgba(0, 0, 0, 0.87)',
};
export const infoColor: SimplePaletteColorOptions = {
  light: '#64b5f6',
  main: '#2196f3',
  dark: '#1976d2',
  contrastText: '#fff',
};

export const grayColor: Color = {
  50: '#fafafa',
  100: '#f5f5f5',
  200: '#eeeeee',
  300: '#e0e0e0',
  400: '#bdbdbd',
  500: '#9e9e9e',
  600: '#757575',
  700: '#616161',
  800: '#424242',
  900: '#212121',
  A100: '#d5d5d5',
  A200: '#aaaaaa',
  A400: '#303030',
  A700: '#616161',
};

const ThemePalette: PaletteOptions = {
  primary: primaryColor,
  action: {
    active: 'rgba(0,0,0,0.54)',
    hover: 'rgba(0,0,0,0.08)',
    hoverOpacity: 0.08,
    selected: 'rgba(0,0,0, 0.14)',
    disabled: 'rgba(0,0,0, 0.26)',
  },
  background: {paper: '#fff', default: '#fafafa'},
  common: {
    black: '#000',
    white: '#fff',
  },
  contrastThreshold: 3,
  divider: 'rgba(0,0,0, 0.12)',
  secondary: secondaryColor,
  error: errorColor,
  warning: warningColor,
  info: infoColor,
  success: successColor,
  grey: grayColor,
  text: {
    primary: 'rgba(0,0,0,0.87)',
    secondary: 'rgba(0,0,0,0.54)',
    disabled: 'rgba(0,0,0,0.38)',
  },
  tonalOffset: 0.2,
};

export const theme = createTheme({
  palette: ThemePalette,
  // レスポンシブのブレイクポイント
  breakpoints: {
    keys: ['xs', 'sm', 'md', 'lg', 'xl'],
    values: {
      xs: 360, // スマホ用
      sm: 768, // タブレット用
      md: 992, // PC用
      lg: 1400,
      xl: 1800,
    },
  },
  typography: {
    // computed = specification * (this.fontSize / 14) * (htmlFontSize / this.htmlFontSize)
    h6: {
      fontSize: '1rem',
    },
  },
  components: {
    MuiAccordion: {
      styleOverrides: {
        root: {
          margin: '16px 0',
        },
      },
    },
    MuiButton: {
      styleOverrides: {
        root: {
          textTransform: 'none', // ボタン内アルファベット文字を大文字変換しない
          height: '100%',
          width: '100%',
        },
      },
      defaultProps: {
        color: 'primary',
        variant: 'contained',
      }
    },
    MuiCard: {
      styleOverrides: {
        root: {
          maxHeight: '100%',
          overflow: 'auto',
          padding: '8px',
        },
      },
    },
    MuiCardContent: {
      styleOverrides: {
        root: {
          padding: '8px',
          '&:last-child': {
            paddingBottom: '8px',
          },
        },
      },
    },
    MuiCardHeader: {
      styleOverrides: {
        root: {
          padding: '8px',
        },
      },
    },
    MuiContainer: {
      styleOverrides: {
        root: {
          padding: '.25em',
        },
      },
    },
    MuiFab: {
      styleOverrides: {
        root: {
          marginLeft: '.25em',
          marginRight: '.25em',
        },
      },
      defaultProps: {
        size: 'medium',
      },
    },
    MuiFormControl: {
      styleOverrides: {
        root: {
          width: '100%',
        },
      },
      defaultProps: {
        variant: 'outlined',
      },
    },
    MuiIcon: {
      styleOverrides: {
        root: {
          display: 'flex',
        },
      },
    },
    MuiPaper: {
      styleOverrides: {
        root: {
          '&.form-paper': {
            padding: '1%',
          },
        },
      },
    },
    MuiRadio: {
      styleOverrides: {
        root: {
          fontSize: '14px',
        },
      },
    },
    MuiTab: {
      styleOverrides: {
        textColorInherit: {
          opacity: 1,
        },
      },
    },
    MuiTableCell: {
      styleOverrides: {
        root: {
          padding: '0.25em 0.5em',
          borderLeft: '1px solid rgba(0, 0, 0, 0.25)',
          borderRight: '1px solid rgba(0, 0, 0, 0.25)',
          borderBottom: '1px solid rgba(0, 0, 0, 0.25)',
          whiteSpace: 'pre',
        },
      },
    },
    MuiTableHead: {
      styleOverrides: {
        root: {
          backgroundColor: primaryColor[50],
          whiteSpace: 'nowrap',
        },
      },
    },
    MuiTableRow: {
      styleOverrides: {
        root: {
          '.MuiTableRow-hover&:hover': {
            backgroundColor: primaryColor[50],
          },
        },
      },
    },
    MuiTypography: {
      styleOverrides: {
        root: {
          '&.error': {
            color: errorColor.main,
          },
        },
      },
    },
    MuiCollapse: {
      defaultProps: {
        timeout: 'auto',
        unmountOnExit: true,
      },
    },
    MuiFormHelperText: {
      defaultProps: {
        margin: 'dense',
      },
    },
    MuiGrid: {
      defaultProps: {
        spacing: 4,
      },
    },
    MuiInputLabel: {
      defaultProps: {
        margin: 'dense',
        variant: 'outlined',
        shrink: true,
      },
    },
    MuiList: {
      defaultProps: {
        dense: true,
      },
    },
    MuiOutlinedInput: {
      defaultProps: {
        margin: 'dense',
        notched: true,
      },
    },
    MuiSelect: {
      defaultProps: {
        variant: 'outlined',
      },
    },
    MuiSwitch: {
      defaultProps: {
        size: 'medium',
      },
    },
    MuiTextField: {
      defaultProps: {
        variant: 'outlined',
        autoComplete: 'off',
      },
    },
  },
  spacing: 4,
});
