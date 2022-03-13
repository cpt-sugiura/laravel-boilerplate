import Box, { BoxProps } from '@mui/material/Box';
import React, { ChangeEvent } from 'react';
import CircularProgress from '@mui/material/CircularProgress';
import { useTrans } from '@/lang/useLangMsg';
import './AppPagination.scss';
import { Pagination } from '@mui/material';

export default React.memo(AppPagination);

type AppPaginationProps = {
  onChangePage: (page: number) => void;
  onChangePerPage?: (perPage: number) => void;
  isLoading?: boolean;
  pagination: {
    currentPage: number;
    from: number;
    lastPage: number;
    perPage: number;
    to: number;
    total: number;
  };
  fitWidth?: boolean;
  RootBoxProps?: BoxProps;
};

/**
 * ページネーション
 * @param props
 * @constructor
 */
function AppPagination(props: AppPaginationProps) {
  const { currentPage, lastPage } = props.pagination;
  const t = useTrans('search.pagination.');
  return (
    <Box
      {...props.RootBoxProps}
      className={`app-pagination ${props.RootBoxProps?.className || ''} ${props.fitWidth ? 'fit-width' : ''}`}
    >
      <div>
        <Pagination
          count={lastPage}
          page={currentPage}
          siblingCount={2}
          onChange={(event: ChangeEvent<unknown>, page: number) => props.onChangePage(page)}
        />
      </div>
      {props.isLoading && (
        <div className="loading">
          <span>{t('searching')}</span>
          <CircularProgress color="primary" size={'1.25em'} />
        </div>
      )}
    </Box>
  );
}
