@php
    $hasDatetime = false;
    foreach($searchMethodsForReact as $m){
        if(isset($m['startName'], $m['endName'])){
            $hasDatetime = true;
            break;
        }
    }
@endphp
import React from 'react';
import { useLocation } from 'react-router';
import qs from 'qs';
import { SearchOption, SearchRequestObject } from '@/common/hook/useSearch';
import { {{ $classBaseName }}SearchResultTable } from '@/{{ lcfirst($domain) }}/component/{{ \Str::camel($classBaseName) }}/{{ $classBaseName }}SearchResultTable';
import { {{ $classBaseName }}SearchBox } from '@/{{ lcfirst($domain) }}/component/{{ \Str::camel($classBaseName) }}/{{ $classBaseName }}SearchBox';
import { useDidMount } from 'beautiful-react-hooks';
import { use{{ ucfirst(\Str::camel($domain)) }}Search } from '@/{{ lcfirst($domain) }}/hook/API/use{{ ucfirst(\Str::camel($domain)) }}Search';
import './{{ $classBaseName }}SearchBoxAndTable.scss';

type {{ $classBaseName }}SearchBoxAndTableProps = {
  searchOptions?: SearchOption;
  makeControlCell?: React.ComponentProps<typeof {{ $classBaseName }}SearchResultTable>['makeControlCell'];
};
export const {{ $classBaseName }}SearchBoxAndTable: React.FC<{{ $classBaseName }}SearchBoxAndTableProps> = (props) => {
  const defaultRequest: Partial<SearchRequestObject> = {
    orderBy: { createdAt: 'desc' },
    ...qs.parse(useLocation().search, { ignoreQueryPrefix: true }),
  };
  const {
    isSearching,
    query,
    pagination,
    searchNewQuery,
    searchWhenChangeOrderBy,
    searchWhenAppendOrderBy,
    searchWhenChangePage,
    searchWhenChangePerPage,
    searchAnyQuery,
    tableData,
  } = use{{ ucfirst(\Str::camel($domain)) }}Search('/{{ \Str::snake($classBaseName) }}', defaultRequest, props.searchOptions);
  useDidMount(() => searchAnyQuery(defaultRequest));
  return (
    <div className={'{{ \Str::kebab($classBaseName) }}-search-paper'}>
      <{{ $classBaseName }}SearchBox clickSearch={searchNewQuery} defaultSearchBoxValues={{ '{'.'{ ...defaultRequest?.search }'.'}' }} />
      <{{ $classBaseName }}SearchResultTable
        isLoading={isSearching}
        tableData={tableData}
        orderBy={query.orderBy}
        pagination={pagination}
        handleRequestSort={searchWhenChangeOrderBy}
        handleChangePage={searchWhenChangePage}
        handleChangePerPage={searchWhenChangePerPage}
        onRequestAppendSort={searchWhenAppendOrderBy}
        makeControlCell={props.makeControlCell}
      />
    </div>
  );
};
