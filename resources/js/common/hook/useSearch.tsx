import { useCallback, useState } from 'react';
import { useNavigate } from 'react-router';
import qs from 'qs';
import { AxiosInstance } from 'axios';

type SearchResponsePagination = {
  total: number;
  perPage: number;
  lastPage: number;
  from: number;
  to: number;
  currentPage: number;
};
export type SearchRequestObject = {
  search: { [key: string]: string | number | Array<string | number> };
  orderBy: { [key: string]: 'asc' | 'desc' };
  page: number;
  perPage: number;
};

export type UseSearchReturn<T> = {
  searchNewQuery: (search: { [key: string]: string | number | Array<string | number> }) => void;
  searchNewQueryPromise: (search: { [key: string]: string | number | Array<string | number> }) => Promise<void>;
  searchWhenChangePage: (pageNumber: number) => void;
  searchWhenChangePerPage: (perPage: number) => void;
  searchWhenChangeOrderBy: (propertyName: string) => void;
  searchAnyQuery: (query: Partial<SearchRequestObject>) => void;
  query: SearchRequestObject;
  tableData: T[];
  pagination: SearchResponsePagination;
  isSearching: boolean;
};

/**
 * 検索機能を使う
 * @param {String} requestUrl 検索APIのURL
 * @param {AxiosInstance} axiosInstance 検索APIを叩くaxiosのインスタンス
 * @param {object} defaultSearchRequest 初期検索条件
 * @param {boolean} withoutWriteURL 検索時にURLを書き換えるか否か
 */
export function useSearch<T = never>(
  requestUrl: string,
  axiosInstance: AxiosInstance,
  defaultSearchRequest: Partial<SearchRequestObject> = {},
  withoutWriteURL = false
): UseSearchReturn<T> {
  const navigate = useNavigate();
  /** 現在の状態で検索する関数 */
  const _searchTableData = useCallback(
    (
      _requestUrl: string,
      _query: SearchRequestObject,
      _setIsSearching: (isSearching: boolean) => void,
      _setTableData: (tableData: []) => void,
      _setPagination: (pagination: SearchResponsePagination) => void
    ): Promise<void> => {
      _setIsSearching(true);
      const _preSearchUrl = window.location.href;
      return axiosInstance
        .get(_requestUrl, { params: _query })
        .then((_response) => {
          if (window.location.href !== _preSearchUrl) {
            // もしアンマウントが確実にされていたら（検索完了前後でURLが違えば） state を更新しない
            return;
          }
          _setTableData(_response.data.body.data);
          delete _response.data.body.data;
          _setPagination(_response.data.body);
          // URL の GET パラメータを変更
          const pushQuery = JSON.parse(JSON.stringify(_query));
          Object.keys(pushQuery.search).forEach((key) => pushQuery.search[key] || delete pushQuery.search[key]);
          pushQuery.page = pushQuery.page || undefined;
          !withoutWriteURL && navigate({ search: qs.stringify(pushQuery) });
        })
        .finally(() => {
          _setIsSearching(false);
        });
    },
    [axiosInstance, navigate]
  );

  const [query, setQuery] = useState<SearchRequestObject>({
    search: {},
    orderBy: { id: 'asc' },
    page: 1,
    perPage: 30,
    ...defaultSearchRequest,
  });
  const [pagination, setPagination] = useState<SearchResponsePagination>({
    currentPage: 1,
    from: 0,
    lastPage: 0,
    perPage: 0, // 現在のクエリ結果の perPage が入るので 0 でOK
    to: 0,
    total: 0,
  });
  const [tableData, setTableData] = useState([]);
  const [isSearching, setIsSearching] = useState(false);

  /** 新しい検索条件で検索する関数 */
  const searchNewQuery = useCallback(
    (search: { [key: string]: string | number | Array<string | number> }): void => {
      const newQuery = { ...query, page: 0, search: JSON.parse(JSON.stringify(search)) };
      setQuery(newQuery);
      _searchTableData(requestUrl, newQuery, setIsSearching, setTableData, setPagination);
    },
    [query, setQuery]
  );

  /** 件数変更による検索 */
  const searchWhenChangePerPage = useCallback(
    (perPage: number): void => {
      setQuery({ ...query, perPage: perPage });
      _searchTableData(requestUrl, { ...query, perPage: perPage }, setIsSearching, setTableData, setPagination);
    },
    [query, setQuery]
  );
  /** ページ移動による検索 */
  const searchWhenChangePage = useCallback(
    (pageNumber: number): void => {
      setQuery({ ...query, page: pageNumber });
      _searchTableData(requestUrl, { ...query, page: pageNumber }, setIsSearching, setTableData, setPagination);
    },
    [query, setQuery]
  );
  /** 並び替えの変更による検索 */
  const searchWhenChangeOrderBy = useCallback(
    (propertyName: string): void => {
      const isAsc =
        query.orderBy && Object.keys(query.orderBy).includes(propertyName) && query.orderBy[propertyName] === 'asc';
      const newQuery: SearchRequestObject = { ...query, orderBy: { [propertyName]: isAsc ? 'desc' : 'asc' }, page: 1 };
      setQuery(newQuery);
      _searchTableData(requestUrl, newQuery, setIsSearching, setTableData, setPagination);
    },
    [query, setQuery]
  );
  return {
    searchNewQuery,
    searchWhenChangePerPage,
    searchWhenChangePage,
    searchWhenChangeOrderBy,
    searchNewQueryPromise: (newQuery) =>
      _searchTableData(requestUrl, { ...query, ...newQuery }, setIsSearching, setTableData, setPagination),
    searchAnyQuery: (newQuery) =>
      _searchTableData(requestUrl, { ...query, ...newQuery }, setIsSearching, setTableData, setPagination),
    query,
    tableData,
    pagination,
    isSearching,
  };
}
