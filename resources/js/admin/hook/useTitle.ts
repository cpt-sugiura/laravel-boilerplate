import { isRouteKey, RouteKey, useAppRouting } from '@/admin/Router';

export const useTitle = (): {
  updateDocumentTitle: (currentUrl: string) => string | undefined;
} => {
  const routing = useAppRouting();
  const updateDocumentTitle = (currentUrl: string): string | undefined => {
    const matchRouteKey = Object.keys(routing).filter((routeKey): routeKey is RouteKey => {
      return isRouteKey(routeKey) && isMatchPathNameAndRealPath(currentUrl, routing[routeKey].path);
    })?.[0];
    let title: string;
    if (!matchRouteKey) {
      title = `管理画面｜ページが見つかりませんでした}`;
    } else {
      title = `管理画面｜${routing?.[matchRouteKey].title}`;
    }
    document.title = title;

    return title;
  };
  return { updateDocumentTitle };
};

/**
 * :hoge などを含むURL定義と実際のURLを比較. 一致すれば true
 * @param routeRelativeUrl 現ページのルート相対URL
 * @param pathWithPattern :hoge を含むルーティング定義
 */
function isMatchPathNameAndRealPath(routeRelativeUrl: string, pathWithPattern: string): boolean {
  if (routeRelativeUrl === '/' || ['', '/'].includes(pathWithPattern)) {
    return routeRelativeUrl === '/' && ['', '/'].includes(pathWithPattern);
  }

  const pathRegExp = pathWithPattern.replace(/:[^/]*/, '[^/]*');
  if (!pathRegExp) {
    return false;
  }

  return new RegExp(`^${pathRegExp}/?$`).test(routeRelativeUrl);
}
