import React from 'react';
import { NavLink } from 'react-router-dom';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import HomeIcon from '@mui/icons-material/Home';
import { useLogout } from '@/admin/hook/useLogout';
import { AppLoading } from '@/common/component/AppLoading';
import { RouteDefine, RouteMenuGroup, useAppRouting, useIsActiveRouteGroup } from '@/admin/Router';
import SecurityIcon from '@mui/icons-material/Security';
import Paper from '@mui/material/Paper';
import { useLocation } from 'react-router';

type ListItemLinkProps = {
  children?: JSX.Element;
  route: RouteDefine;
  group: RouteMenuGroup;
  title?: string;
};
const EmptyIcon = () => <div className={'MuiSvgIcon-root'} />;

const ListItemLink: React.FC<ListItemLinkProps> = (props) => {
  const location = useLocation();
  const isActive = useIsActiveRouteGroup(props.group, location.pathname);
  return (
    <NavLink className={() => (isActive ? 'nav-link-active' : '')} to={props.route.path}>
      <ListItem button>
        <ListItemIcon>{props.children || <EmptyIcon />}</ListItemIcon>
        <ListItemText primary={props.title || props.route.title} />
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
    <Paper className={'app-menu-bar'}>
      <List className="list">
        <ListItemLink route={AppRouting.home} group={'home'}>
          <HomeIcon />
        </ListItemLink>
        <ListItemLink route={AppRouting.adminSearch} group={'admin'} title={'管理者'}>
          <SecurityIcon />
        </ListItemLink>
        <LogoutLink />
      </List>
    </Paper>
  );
};
