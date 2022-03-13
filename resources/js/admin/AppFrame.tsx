import React, { CSSProperties } from 'react';
import { Container, CssBaseline } from '@mui/material';
import { AppMenuBar } from '@/admin/AppMenuBar';
import { AppHeader } from '@/admin/AppHeader';
import './AppFrame.scss';

export const SIDEBAR_WIDTH = 240;
export const HEADER_HEIGHT: CSSProperties['height'] = '3em';

/**
 * 大枠
 */
const AppFrame: React.FC<{ children: JSX.Element }> = (props) => {
  return (
    <div className="app-frame">
      <CssBaseline />
      <AppHeader />
      <div className={'app-body'}>
        <nav className={'drawer'}>
          <AppMenuBar />
        </nav>
        <main className={'content'}>
          <Container maxWidth="lg">{props.children}</Container>
        </main>
      </div>
    </div>
  );
};

export { AppFrame };
