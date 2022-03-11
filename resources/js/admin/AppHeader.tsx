import React, { CSSProperties, useEffect, useState } from 'react';
import { AppBar } from '@mui/material';
import { useLocation } from 'react-router';
import { isRouteKey, RouteDefine, RouteKey, useAppRouting } from '@/admin/Router';
import './AppHeader.scss'

const updateTitle = (routing: Record<RouteKey, RouteDefine>, currentUrl: string): string | undefined => {
  const matchRouteKey: RouteKey = Object.keys(routing).filter((routeKey): routeKey is RouteKey => {
    return isRouteKey(routeKey) && routing[routeKey].match(currentUrl);
  })?.[0];
  const title = `管理画面｜${matchRouteKey && routing?.[matchRouteKey].title}`;
  document.title = title;

  return title;
};

const AppHeader: React.FC<{ appBarStyle?: CSSProperties }> = ({ appBarStyle }) => {
  const location = useLocation();
  const routing = useAppRouting();
  const [title, setTitle] = useState(updateTitle(routing, location.pathname));
  useEffect(() => {
    setTitle(updateTitle(routing, location.pathname));
  }, [location.pathname]);

  return (
    <AppBar className={'app-header'} style={appBarStyle} elevation={0}>
      <span className={'title'}>{title}</span>
    </AppBar>
  );
};

export { AppHeader };
