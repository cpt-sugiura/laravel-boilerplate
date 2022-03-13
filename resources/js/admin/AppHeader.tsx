import React, { useEffect, useState } from 'react';
import { useLocation } from 'react-router';
import { isRouteKey, RouteDefine, RouteKey, useAppRouting } from '@/admin/Router';
import './AppHeader.scss';
import { useLoginAdminContext } from '@/admin/context/LoginAdminContext';

const updateTitle = (routing: Record<RouteKey, RouteDefine>, currentUrl: string): string | undefined => {
  const matchRouteKey: RouteKey = Object.keys(routing).filter((routeKey): routeKey is RouteKey => {
    return isRouteKey(routeKey) && routing[routeKey].match(currentUrl);
  })?.[0];
  const title = `管理画面｜${matchRouteKey && routing?.[matchRouteKey].title}`;
  document.title = title;

  return title;
};

const AppHeader: React.FC = () => {
  const location = useLocation();
  const routing = useAppRouting();
  const [title, setTitle] = useState(updateTitle(routing, location.pathname));
  useEffect(() => {
    setTitle(updateTitle(routing, location.pathname));
  }, [location.pathname]);
  const { loginAdmin } = useLoginAdminContext();
  return (
    <div className={'app-header'}>
      <span className={'title'}>{title}</span>
      <span>{loginAdmin.name}</span>
    </div>
  );
};

export { AppHeader };
