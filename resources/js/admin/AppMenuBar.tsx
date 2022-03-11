import React from 'react';
import Drawer from '@mui/material/Drawer';
import { NavLink } from 'react-router-dom';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import { HEADER_HEIGHT, SIDEBAR_WIDTH } from '@/admin/AppFrame';
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
    <NavLink className={(isActive)=>isActive? 'nav-link-active' : ''} to={route.path}>
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
