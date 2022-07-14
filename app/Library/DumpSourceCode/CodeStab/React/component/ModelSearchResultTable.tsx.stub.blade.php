import React from 'react';
import Paper from '@mui/material/Paper';
import { SearchTable } from '@/common/component/search/SearchTable';
import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';
import { useAppRouting } from '@/{{ lcfirst($domain) }}/Router';
import { useDateTimeFormatters } from '@/{{ lcfirst($domain) }}/hook/useDateTimeFormatters';
import { RowBox } from '@/common/component/RowBox';
import { LinkBtn } from '@/account/component/_common/LinkBtn';

export type {{ $classBaseName }}SearchTableRow = {
  {{ \Str::camel($primaryKey) }}: number;
@foreach( array_filter($columnsForRender, fn(\App\Library\DumpSourceCode\ColumnForRender $c) => $c->searchable() && $c->column->getName() !== $primaryKey ) as $c )
  {{ $c->getNameAsRequestKey() }}: {!! $c->getTypeScriptType() !!};
@endforeach
};
type {{ $classBaseName }}SearchTableProps = {
  isLoading: boolean;
  handleRequestSort: (columnName: string) => void;
  onRequestAppendSort: (columnName: string) => void;
  handleChangePage: (page: number) => void;
  handleChangePerPage: (perPage: number) => void;
  orderBy: { [key: string]: 'asc' | 'desc' };
  tableData: Array<{{ $classBaseName }}SearchTableRow>;
  makeControlCell?: (props: { {{ lcfirst($classBaseName) }}Row: {{ $classBaseName }}SearchTableRow }) => JSX.Element;
  pagination: {
    currentPage: number;
    from: number;
    lastPage: number;
    perPage: number;
    to: number;
    total: number;
  };
};

const ControlCell = ({ {{ \Str::camel($primaryKey) }} }: { {{ \Str::camel($primaryKey) }}: number }) => {
  const routing = useAppRouting();
  return (
    <RowBox>
      <LinkBtn toRoute={routing.{{ \Str::camel($classBaseName) }}Show} params={ ({ {{ \Str::camel($primaryKey) }} })}>編集</LinkBtn>
    </RowBox>
  );
};

/**
 * {{ $classFullName::getNaturalLanguageName() }}検索結果テーブル
 */
function {{ $classBaseName }}SearchResultTableComponent(props: {{ $classBaseName }}SearchTableProps): JSX.Element {
  const { dmf } = useDateTimeFormatters();
  return (
    <Paper>
      <SearchTable
        isLoading={props.isLoading}
        handleRequestSort={props.handleRequestSort}
        onRequestAppendSort={props.onRequestAppendSort}
        onChangePage={props.handleChangePage}
        onChangePerPage={props.handleChangePerPage}
        orderBy={props.orderBy}
        tableData={props.tableData}
        pagination={props.pagination}
        headCells={[
@foreach( array_filter($columnsForRender, fn(\App\Library\DumpSourceCode\ColumnForRender $c) => $c->searchable() && $c->column->getName() !== $primaryKey ) as $c )
          { id: '{{ $c->getNameAsRequestKey() }}', label: '{{ $c->getLabel() }}' },
@endforeach
          { label: '操作' },
        ]}
      >
        {props.tableData.map((row) => {
          return (
            <TableRow hover key={{ '{row.'. \Str::camel($primaryKey) .'}' }}>
@foreach( array_filter($columnsForRender, fn(\App\Library\DumpSourceCode\ColumnForRender $c) => $c->searchable() && $c->column->getName() !== $primaryKey ) as $c )
@if( in_array( $c->column->getType()->getName(), ['date', 'datetime', 'timestamp'], true))
              <TableCell>{dmf(row.{{ $c->getNameAsRequestKey() }})}</TableCell>
@elseif( $c->column->getType()->getName() === 'boolean' )
              <TableCell>{row.{{ $c->getNameAsRequestKey() }} ? '〇' : ''}</TableCell>
@else
              <TableCell>{row.{{ $c->getNameAsRequestKey() }}}</TableCell>
@endif
@endforeach
              <TableCell className={'controller-cell-root'}>
                {props.makeControlCell ? (
                  props.makeControlCell({ {{ lcfirst($classBaseName) }}Row: row })
                ) : (
                  <ControlCell {{ \Str::camel($primaryKey) }}={row.{{ \Str::camel($primaryKey) }}} />
                )}
              </TableCell>
            </TableRow>
          );
        })}
      </SearchTable>
    </Paper>
  );
}

export const {{ $classBaseName }}SearchResultTable = React.memo({{ $classBaseName }}SearchResultTableComponent);
