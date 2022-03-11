import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';
import TableSortLabel from '@mui/material/TableSortLabel';
import React from 'react';

export type SearchTableHeadCell = {
  id?: string;
  label: string | JSX.Element;
  numeric?: boolean;
  disablePadding?: boolean;
};
type AppTableHeadProps = {
  onRequestSort: (columnName: string) => void;
  orderBy: { [key: string]: 'asc' | 'desc' };
  headCells: SearchTableHeadCell[];
};

/**
 * 検索結果表コンポーネント
 */
export function AppTableHead(props: AppTableHeadProps): JSX.Element {
  const createSortHandler = (columnName: string) => () => {
    props.onRequestSort(columnName);
  };
  const headCells = props.headCells.map((headCell) => {
    return {
      id: headCell.id || null,
      label: headCell.label,
      numeric: headCell.numeric || false,
      disablePadding: headCell.disablePadding || false,
    };
  });

  const orderByKeys = Object.keys(props.orderBy);

  return (
    <TableHead>
      <TableRow>
        {headCells.map((headCell, index) => (
          <TableCell
            key={index}
            align={headCell.numeric ? 'right' : 'left'}
            padding={headCell.disablePadding ? 'none' : undefined}
            sortDirection={headCell.id && orderByKeys.includes(headCell.id) ? props.orderBy[headCell.id] : false}
            onClick={headCell.id ? createSortHandler(headCell.id) : () => null}
          >
            {headCell.id ? (
              <TableSortLabel
                active={orderByKeys.includes(headCell.id)}
                direction={orderByKeys.includes(headCell.id) ? props.orderBy[headCell.id] : 'asc'}
              >
                {headCell.label}
              </TableSortLabel>
            ) : (
              <span>{headCell.label}</span>
            )}
          </TableCell>
        ))}
      </TableRow>
    </TableHead>
  );
}
