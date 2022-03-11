import React, { CSSProperties } from 'react';
import { Container, CssBaseline } from '@mui/material';
import { AppMenuBar } from '@/admin/AppMenuBar';
import { makeStyles } from '@mui/styles';
import { AppHeader } from '@/admin/AppHeader';

export const SIDEBAR_WIDTH = 240;
export const HEADER_HEIGHT: CSSProperties['height'] = '3em';



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
