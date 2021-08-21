import React from 'react';
import { useLocation } from 'react-router';
import qs from 'qs';
import { SearchRequestObject, useSearch } from '@/common/hook/useSearch';
import { useAdminAxios } from '@/admin/hook/API/useAdminAxios';
import { AdminSearchResultTable } from '@/admin/component/admin/AdminSearchResultTable';
import { AdminSearchBox } from '@/admin/component/admin/AdminSearchBox';

const AdminSearchPageComponent: React.FC = () => {
  const defaultRequest: Partial<SearchRequestObject> = {
    orderBy: { createdAt: 'desc' },
    ...qs.parse(useLocation().search, { ignoreQueryPrefix: true }),
  };
  const { axiosInstance } = useAdminAxios();
  const {
    isSearching,
    query,
    pagination,
    searchNewQuery,
    searchWhenChangeOrderBy,
    searchWhenChangePage,
    searchWhenChangePerPage,
    searchAnyQuery,
    tableData,
  } = useSearch('/admin', axiosInstance, defaultRequest);
  React.useEffect(() => searchAnyQuery(defaultRequest), []);

  return (
    <React.Fragment>
      <AdminSearchBox clickSearch={searchNewQuery} defaultSearchBoxValues={{ ...defaultRequest?.search }} />
      <AdminSearchResultTable
        isLoading={isSearching}
        tableData={tableData}
        orderBy={query.orderBy}
        pagination={pagination}
        handleRequestSort={searchWhenChangeOrderBy}
        handleChangePage={searchWhenChangePage}
        handleChangePerPage={searchWhenChangePerPage}
      />
    </React.Fragment>
  );
};

export const AdminSearchPage = React.memo(AdminSearchPageComponent);
