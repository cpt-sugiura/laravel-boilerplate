import React from 'react';
import Paper from '@material-ui/core/Paper';
import { SearchTable } from '@/common/component/search/SearchTable';
import TableRow from '@material-ui/core/TableRow';
import TableCell from '@material-ui/core/TableCell';
import Grid from '@material-ui/core/Grid';
import { useHistory } from 'react-router';
import Button from '@material-ui/core/Button';
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
  const history = useHistory();
  const routing = useAppRouting();
  const handleClick = () => history.push(makeRoutePath(routing.adminShow, { adminId }));
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
