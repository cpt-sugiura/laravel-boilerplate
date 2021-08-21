import React, { CSSProperties } from 'react';
import { Container, CssBaseline } from '@material-ui/core';
import { AppMenuBar } from '@/admin/AppMenuBar';
import makeStyles from '@material-ui/core/styles/makeStyles';
import { AppHeader } from '@/admin/AppHeader';

export const SIDEBAR_WIDTH = 240;
export const HEADER_HEIGHT: CSSProperties['height'] = '3em';

const useStyles = makeStyles((theme) => ({
  root: {
    display: 'flex',
  },
  drawer: {
    [theme.breakpoints.up('sm')]: {
      width: SIDEBAR_WIDTH,
      flexShrink: 0,
    },
  },
  content: {
    flexGrow: 1,
    padding: theme.spacing(3),
    paddingTop: `calc(${HEADER_HEIGHT} + ${theme.spacing(3)}px)`,
    maxWidth: `calc(100% - ${SIDEBAR_WIDTH}px)`,
  },
}));

/**
 * 大枠
 */
const AppFrame: React.FC<{ children: JSX.Element }> = (props) => {
  const classes = useStyles();

  return (
    <div className={classes.root}>
      <CssBaseline />
      <AppHeader appBarStyle={{ height: HEADER_HEIGHT }} />
      <nav className={classes.drawer}>
        <AppMenuBar />
      </nav>
      <main className={classes.content}>
        <Container maxWidth="lg">{props.children}</Container>
      </main>
    </div>
  );
};

export { AppFrame };
