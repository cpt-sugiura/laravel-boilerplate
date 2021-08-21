import React from 'react';
import Drawer from '@material-ui/core/Drawer';
import { NavLink, NavLinkProps } from 'react-router-dom';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import makeStyles from '@material-ui/core/styles/makeStyles';
import { HEADER_HEIGHT, SIDEBAR_WIDTH } from '@/admin/AppFrame';
import HomeIcon from '@material-ui/icons/Home';
import { useLogout } from '@/admin/hook/useLogout';
import { AppLoading } from '@/common/component/AppLoading';
import { RouteDefine, RouteMenuGroup, useAppRouting, useIsActiveRouteGroup } from '@/admin/Router';
import SecurityIcon from '@material-ui/icons/Security';

type ListItemLinkProps = {
  children?: JSX.Element;
  route: RouteDefine;
  group: RouteMenuGroup;
  title?: string;
};
const EmptyIcon = () => <div className={'MuiSvgIcon-root'} />;

const useMakeIsActive =
  (group: RouteMenuGroup): NavLinkProps['isActive'] =>
  (match, location): boolean => {
    return useIsActiveRouteGroup(group, location.pathname);
  };
const ListItemLink: React.FC<ListItemLinkProps> = ({ children, route, group, title }) => {
  const isActive = useMakeIsActive(group);
  return (
    <NavLink activeClassName={'nav-link-active'} isActive={isActive} to={route.path}>
      <ListItem button>
        <ListItemIcon>{children || <EmptyIcon />}</ListItemIcon>
        <ListItemText primary={title || route.title} />
      </ListItem>
    </NavLink>
  );
};

const useStyles = makeStyles(() => ({
  drawerPaper: {
    width: SIDEBAR_WIDTH,
    top: HEADER_HEIGHT,
    height: `calc(100% - ${HEADER_HEIGHT})`,
  },
  list: {
    height: '100%',
  },
}));

const LogoutLink: React.FC = () => {
  const { logoutAction, logoutLoading } = useLogout();
  return (
    <ListItem button onClick={logoutAction}>
      {logoutLoading ? <AppLoading message={'ログアウト中'} /> : <ListItemText primary={'ログアウト'} />}
    </ListItem>
  );
};
export const AppMenuBar: React.FC = () => {
  const classes = useStyles();
  const AppRouting = useAppRouting();

  return (
    <Drawer
      classes={{
        paper: classes.drawerPaper,
      }}
      variant="permanent"
    >
      <List classes={{ root: classes.list }}>
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
