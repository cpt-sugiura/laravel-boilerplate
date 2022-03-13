import React from 'react';
import Paper from '@mui/material/Paper';
import { SearchTable } from '@/common/component/search/SearchTable';
import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';
import Grid from '@mui/material/Grid';
import { useNavigate } from 'react-router';
import Button from '@mui/material/Button';
import { makeRoutePath, useAppRouting } from '@/admin/Router';
import { useDateTimeFormatters } from '@/admin/hook/useDateTimeFormatters';

type AdminSearchTableRow = {
  adminId: number;
  name: string;
  email: string;
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
 * 管理者検索結果テーブル
 */
function AdminSearchResultTableComponent(props: AdminSearchTableProps): JSX.Element {
  const { dmf } = useDateTimeFormatters();
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
        headCells={[
          { id: 'name', label: '名前' },
          { id: 'email', label: 'メールアドレス' },
          { id: 'createdAt', label: '作成日時' },
          { label: '操作' },
        ]}
      >
        {props.tableData.map((row, index) => {
          return (
            <TableRow hover key={index}>
              <TableCell>{row.name}</TableCell>
              <TableCell>{row.email}</TableCell>
              <TableCell>{dmf(row.createdAt)}</TableCell>
              <TableCell className={'controller-cell-root'}>
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
