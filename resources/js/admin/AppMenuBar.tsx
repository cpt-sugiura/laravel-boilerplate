import React from 'react';
import Drawer from '@mui/material/Drawer';
import { NavLink } from 'react-router-dom';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import HomeIcon from '@mui/icons-material/Home';
import { useLogout } from '@/admin/hook/useLogout';
import { AppLoading } from '@/common/component/AppLoading';
import { RouteDefine, RouteMenuGroup, useAppRouting } from '@/admin/Router';
import SecurityIcon from '@mui/icons-material/Security';

type ListItemLinkProps = {
  children?: JSX.Element;
  route: RouteDefine;
  group: RouteMenuGroup;
  title?: string;
};
const EmptyIcon = () => <div className={'MuiSvgIcon-root'} />;

const ListItemLink: React.FC<ListItemLinkProps> = ({ children, route, title }) => {
  // const isActive = useIsActiveRouteGroup(group, );
  return (
    <NavLink className={(isActive) => (isActive ? 'nav-link-active' : '')} to={route.path}>
      <ListItem button>
        <ListItemIcon>{children || <EmptyIcon />}</ListItemIcon>
        <ListItemText primary={title || route.title} />
      </ListItem>
    </NavLink>
  );
};

const LogoutLink: React.FC = () => {
  const { logoutAction, logoutLoading } = useLogout();
  return (
    <ListItem button onClick={logoutAction}>
      {logoutLoading ? <AppLoading message={'ログアウト中'} /> : <ListItemText primary={'ログアウト'} />}
    </ListItem>
  );
};
export const AppMenuBar: React.FC = () => {
  const AppRouting = useAppRouting();

  return (
    <Drawer className={'app-menu-bar'} variant="permanent">
      <List className="list">
        <ListItemLink route={AppRouting.home} group={'home'}>
          <HomeIcon />
        </ListItemLink>
        <ListItemLink route={AppRouting.adminSearch} group={'admin'} title={'管理者'}>
          <SecurityIcon />
        </ListItemLink>
        <LogoutLink />
      </List>
    </Drawer>
  );
};
