import * as React from 'react';
import { PropsWithChildren } from 'react';
import Paper from '@material-ui/core/Paper';
import TableContainer from '@material-ui/core/TableContainer';
import Table from '@material-ui/core/Table';
import { AppTableHead, SearchTableHeadCell } from './AppTableHead';
import TableBody from '@material-ui/core/TableBody';

export type SimpleSearchTableProps = PropsWithChildren<{
  headCells: SearchTableHeadCell[];
}>;
export const SimpleSearchTable: React.FC<SimpleSearchTableProps> = (props) => {
  return (
    <Paper>
      <div style={{ position: 'relative' }} className={'search-result-table'}>
        <TableContainer>
          <Table>
            <AppTableHead orderBy={{}} onRequestSort={() => null} headCells={props.headCells} />
            <TableBody>{props.children}</TableBody>
          </Table>
        </TableContainer>
      </div>
    </Paper>
  );
};
