@php
$hasDatetime = false;
foreach($searchMethodsForReact as $m){
    if(isset($m['startName'], $m['endName'])){
        $hasDatetime = true;
        break;
    }
}
@endphp
import React, { ChangeEvent, useState } from 'react';
import Paper from '@mui/material/Paper';
import { RowBox } from '@/common/component/RowBox';
import { SearchBtn } from '@/{{ lcfirst($domain) }}/component/_common/SearchBtn';
import { ResetBtn } from '@/{{ lcfirst($domain) }}/component/_common/ResetBtn';
import { AppTextField } from '@/common/component/fields/AppTextField';
import './{{ $classBaseName }}SearchBox.scss';
@if( in_array('DateRange', array_column($searchMethodsForReact, 'type'), true))
import { AppDateRangeInput } from '@/common/component/fields/AppDateRangeInput';
@endif
@if( in_array('Boolean', array_column($searchMethodsForReact, 'type'), true))
import { AppRadio } from '@/common/component/fields/AppRadio';
@endif
import { useOnEnterKey } from '@/common/hook/useOnEnterKey';

type {{ $classBaseName }}SearchBoxProps = {
  clickSearch: (searchBox: { [key: string]: string | number | Array<string | number> }) => void;
  defaultSearchBoxValues?: { [key: string]: string | number | Array<string | number> };
};

export const {{ $classBaseName }}SearchBox = (props: {{ $classBaseName }}SearchBoxProps): JSX.Element => {
  const [searchBox, setSearchBox] = useState({
@foreach( $searchMethodsForReact as $method)
    @if( !isset($method['startName'], $method['endName']) )
    {{ $method['name'] }}: '',
    @endif
    @isset( $method['startName'] )
    {{ $method['startName'] }}: '',
    @endisset
    @isset( $method['endName'] )
    {{ $method['endName'] }}: '',
    @endisset
@endforeach
    ...props.defaultSearchBoxValues,
  });
  const updateState = (
    event:
      | ChangeEvent<HTMLInputElement>
      | React.ChangeEvent<{ name?: string; value: string }>
      | { target: { name?: string; value: string | number } }
  ) => {
    if (!event.target.name) {
      return;
    }
    setSearchBox({
      ...searchBox,
      [event.target.name]: event.target.value,
    });
  };
  @if( $hasDatetime )
  const [clearSearchTrigger, setClearSearchTrigger] = useState('');
  @endif
  const clearSearch = () => {
    @if( $hasDatetime )
    setClearSearchTrigger(`${Math.random()}`);
    @endif
    setSearchBox({
@foreach( $searchMethodsForReact as $method)
    @if( !isset($method['startName'], $method['endName']) )
        {{ $method['name'] }}: '',
    @endif
    @isset( $method['startName'] )
        {{ $method['startName'] }}: '',
    @endisset
    @isset( $method['endName'] )
        {{ $method['endName'] }}: '',
    @endisset
@endforeach
    });
  };

  const { onEnterKey } = useOnEnterKey(() => props.clickSearch(searchBox));
  return (
    <Paper onKeyDown={onEnterKey} className="{{ \Str::kebab(lcfirst($classBaseName)) }}-search-box search-box" elevation={3}>
@foreach( $searchMethodsForReact as $method)
    @isset( $method['rendered'] )
      {!! $method['rendered'] !!}
    @else
      <AppTextField label="{{ $method['label'] }}" name="{{ $method['name'] }}" onChange={updateState} value={searchBox.{{ $method['name'] }}} />
    @endisset
@endforeach
      <RowBox className={'control'}>
        <SearchBtn onClick={() => props.clickSearch(searchBox)} />
        <ResetBtn onClick={clearSearch} />
      </RowBox>
    </Paper>
  );
};
