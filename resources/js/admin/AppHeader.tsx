import React, { CSSProperties, useEffect, useState } from 'react';
import { AppBar } from '@material-ui/core';
import makeStyles from '@material-ui/core/styles/makeStyles';
import { useLocation } from 'react-router';
import { isRouteKey, RouteDefine, RouteKey, useAppRouting } from '@/admin/Router';

const updateTitle = (routing: Record<RouteKey, RouteDefine>, currentUrl: string): string | undefined => {
  const matchRouteKey: RouteKey = Object.keys(routing).filter((routeKey): routeKey is RouteKey => {
    return isRouteKey(routeKey) && routing[routeKey].match(currentUrl);
  })?.[0];
  const title = `管理画面｜${matchRouteKey && routing?.[matchRouteKey].title}`;
  document.title = title;

  return title;
};

const useStyles = makeStyles(() => ({
  'app-header': {
    display: 'flex',
    flexDirection: 'row',
    alignItems: 'center',
    paddingLeft: '1em',
    flexWrap: 'wrap',
    height: '3em',
    top: 0,
  },
  title: {
    flexGrow: 1,
  },
}));

const AppHeader: React.FC<{ appBarStyle?: CSSProperties }> = ({ appBarStyle }) => {
  const classes = useStyles();

  const location = useLocation();
  const routing = useAppRouting();
  const [title, setTitle] = useState(updateTitle(routing, location.pathname));
  useEffect(() => {
    setTitle(updateTitle(routing, location.pathname));
  }, [location.pathname]);

  return (
    <AppBar className={classes['app-header']} style={appBarStyle} elevation={0}>
      <span>{title}</span>
    </AppBar>
  );
};

export { AppHeader };
