import * as React from 'react';
import { PropsWithChildren } from 'react';
import Paper from '@mui/material/Paper';
import TableContainer from '@mui/material/TableContainer';
import Table from '@mui/material/Table';
import { AppTableHead, SearchTableHeadCell } from './AppTableHead';
import TableBody from '@mui/material/TableBody';
import AppPagination from './AppPagination';

export type SearchTableProps = PropsWithChildren<{
  isLoading: boolean;
  handleRequestSort: (columnName: string) => void;
  onChangePage: (page: number) => void;
  onChangePerPage: (perPage: number) => void;
  orderBy: { [key: string]: 'asc' | 'desc' };
  tableData: unknown[];
  pagination: {
    currentPage: number;
    from: number;
    lastPage: number;
    perPage: number;
    to: number;
    total: number;
  };
  headCells: SearchTableHeadCell[];
}>;
export const SearchTable = SearchTableComponent;
/**
 * 検索結果テーブル
 * @param props
 */
function SearchTableComponent(props: SearchTableProps): JSX.Element {
  return (
    <Paper>
      <AppPagination
        pagination={props.pagination}
        onChangePage={(page) => props.onChangePage(page)}
        onChangePerPage={(page) => props.onChangePerPage(page)}
        isLoading={props.isLoading}
      />
      <div style={{ position: 'relative' }} className={'search-result-table'}>
        <TableContainer>
          <Table>
            <AppTableHead orderBy={props.orderBy} onRequestSort={props.handleRequestSort} headCells={props.headCells} />
            <TableBody>{props.children}</TableBody>
          </Table>
        </TableContainer>
      </div>
      <AppPagination
        pagination={props.pagination}
        onChangePage={(page) => props.onChangePage(page)}
        onChangePerPage={(page) => props.onChangePerPage(page)}
        isLoading={props.isLoading}
      />
    </Paper>
  );
}
