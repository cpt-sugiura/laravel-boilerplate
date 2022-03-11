import React from 'react';
import Paper from '@mui/material/Paper';
import { SearchTable } from '@/common/component/search/SearchTable';
import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';
import Grid from '@mui/material/Grid';
import { useNavigate } from 'react-router';
import Button from '@mui/material/Button';
import { makeRoutePath, useAppRouting } from '@/admin/Router';

type AdminSearchTableRow = {
  adminId: number;
  name: string;
  createdAt: string;
};
type AdminSearchTableProps = {
  isLoading: boolean;
  handleRequestSort: (columnName: string) => void;
  handleChangePage: (page: number) => void;
  handleChangePerPage: (perPage: number) => void;
  orderBy: { [key: string]: 'asc' | 'desc' };
  tableData: Array<AdminSearchTableRow>;
  pagination: {
    currentPage: number;
    from: number;
    lastPage: number;
    perPage: number;
    to: number;
    total: number;
  };
};

const ControlCell = ({ adminId }: { adminId: number }) => {
  const navigate = useNavigate();
  const routing = useAppRouting();
  const handleClick = () => navigate(makeRoutePath(routing.adminShow, { adminId }));
  return (
    <Grid container>
      <Grid item xs={6}>
        <Button onClick={handleClick}>編集</Button>
      </Grid>
    </Grid>
  );
};

/**
 * 検索結果テーブル
 * @param props
 * @constructor
 */
function AdminSearchResultTableComponent(props: AdminSearchTableProps): JSX.Element {
  return (
    <Paper>
      <SearchTable
        isLoading={props.isLoading}
        handleRequestSort={props.handleRequestSort}
        onChangePage={props.handleChangePage}
        onChangePerPage={props.handleChangePerPage}
        orderBy={props.orderBy}
        tableData={props.tableData}
        pagination={props.pagination}
        headCells={[{ id: 'name', label: '名前' }, { id: 'createdAt', label: '作成日時' }, { label: '操作' }]}
      >
        {props.tableData.map((row, index) => {
          return (
            <TableRow hover tabIndex={-1} key={index}>
              <TableCell align="left">{row.name}</TableCell>
              <TableCell align="left">{row.createdAt}</TableCell>
              <TableCell align="left" className={'controller-cell-root'}>
                <ControlCell adminId={row.adminId} />
              </TableCell>
            </TableRow>
          );
        })}
      </SearchTable>
    </Paper>
  );
}

export const AdminSearchResultTable = React.memo(AdminSearchResultTableComponent);
