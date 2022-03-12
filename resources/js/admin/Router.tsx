import { Route, UNSAFE_NavigationContext } from 'react-router-dom';
import { Routes } from 'react-router';
import React, { useContext, useEffect } from 'react';
import { BrowserHistory } from 'history';
import { useTitle } from '@/admin/hook/useTitle';
import { HomePage } from '@/admin/pages/HomePage';
import { NotFound } from '@/admin/pages/NotFound';
import { AdminSearchPage } from '@/admin/pages/admin/AdminSearchPage';
import { AdminCreatePage } from '@/admin/pages/admin/AdminCreatePage';
import { AdminShowPage } from '@/admin/pages/admin/AdminShowPage';

const AppRouter = React.memo(RouterComponent);

type RouteDefine = {
  path: string;
  title: string;
  match: (url: string) => boolean; // 主にメニューバーのアクティブ非アクティブの判別用の match
};
const RouteKeyList = ['home', 'adminSearch', 'adminShow', 'adminCreate'] as const;
type RouteKey = typeof RouteKeyList[number];
/** typescript の都合で as RouteKey にしているが実際は不定。不定が RouteKey か確認したい */
export const isRouteKey = (key: string): key is RouteKey => RouteKeyList.includes(key as RouteKey);
/** URLマッチ定義で使う */
const makeUrlMatcher =
  (pattern: RegExp | string) =>
  (url: string): boolean =>
    new RegExp(pattern).test(url);
/** ルーティング定義 */
const useAppRouting = (): Record<RouteKey, RouteDefine> => {
  return {
    home: { path: '/', title: 'ホーム', match: (url) => !!url.match(/^\/?$/) },
    adminSearch: { path: '/admin', title: '管理者検索', match: makeUrlMatcher('^/admin/?$') },
    adminCreate: { path: '/admin/create', title: '管理者作成', match: makeUrlMatcher('^/admin/create/?$') },
    adminShow: { path: '/admin/:adminId', title: '管理者詳細', match: makeUrlMatcher('^/admin/\\d+/?$') },
  };
};

/**
 * react-router で定義されるルーティングパラメータの置き換え
 * 文字列結合でURL構築する羽目になるのを防止
 */
const makeRoutePath = (route: RouteDefine, params: { [key: string]: string | number } = {}): string => {
  let returnPath = route.path;
  Object.keys(params).forEach((key) => (returnPath = returnPath.replace(new RegExp(`:${key}`), `${params[key]}`)));
  return returnPath;
};
/** menu とルーティングの連動でルーティング定義によっている者ら */
type RouteMenuGroup = 'home' | 'admin';
const useActiveGroup = (): Record<RouteMenuGroup, RouteDefine[]> => {
  const routing = useAppRouting();
  return {
    home: [routing.home],
    admin: [routing.adminSearch, routing.adminCreate, routing.adminShow],
  };
};
const useIsActiveRouteGroup = (group: RouteMenuGroup | undefined | null, url: string): boolean => {
  if (!group) {
    return false;
  }
  const ActiveGroup = useActiveGroup();
  return ActiveGroup[group].filter((r) => r.match(url)).length > 0;
};

function RouterComponent(): JSX.Element {
  const AppRouting = useAppRouting();
  const { updateDocumentTitle } = useTitle();
  useEffect(() => {
    updateDocumentTitle(location.pathname);
  }, []);
  const navigation = useContext(UNSAFE_NavigationContext).navigator as BrowserHistory;
  React.useLayoutEffect(() => {
    if (navigation) {
      navigation.listen((locationListener) => updateDocumentTitle(locationListener.location.pathname));
    }
  }, [navigation]);

  return (
    <Routes>
      <Route path={AppRouting.home.path} element={HomePage} />

      <Route path={AppRouting.adminSearch.path} element={AdminSearchPage} />
      <Route path={AppRouting.adminCreate.path} element={AdminCreatePage} />
      <Route path={AppRouting.adminShow.path} element={AdminShowPage} />
      <Route path="*">
        <NotFound />
      </Route>
    </Routes>
  );
}
export { AppRouter, useAppRouting, makeRoutePath, useIsActiveRouteGroup };
export type { RouteDefine, RouteKey, RouteMenuGroup };
