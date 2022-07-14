@php
    /** @var \App\Library\DumpSourceCode\ColumnForRender[] $columnsForRender */
@endphp
import React from 'react';
import { AppFormTextField } from '@/common/component/form/AppFormTextField';
@if( !empty(array_filter($columnsForRender, fn($c)=>$c->getCastType() === 'boolean' )) )
import { AppFormRadio } from '@/common/component/form/AppFormRadio';
@endif
@if( !empty(array_filter($columnsForRender, fn($c)=>str_contains($c->column->getComment() ?? '', '@enum:')  )) )
import { AppFormSelect } from '@/common/component/form/AppFormSelect';
@endif
import './{{ $classBaseName }}FormFields.scss';

type {{ $classBaseName }}FormInputTypes = {
@foreach( $columnsForRender as $c )
   @if( $c->editable() )
  {{ $c->getNameAsRequestKey() }}: {!! $c->getTypeScriptType() !!};
  @endif
@endforeach
};

const to{{ $classBaseName }}FormInputTypes = (any: Partial<{{ $classBaseName }}FormInputTypes>): {{ $classBaseName }}FormInputTypes => {
  return {
@foreach( $columnsForRender as $c )
    @if( $c->editable() )
    {{ $c->getNameAsRequestKey() }}: any.{{ $c->getNameAsRequestKey() }} || {!! $c->getTypeScriptDefaultValue() !!},
    @endif
@endforeach
  };
};

function {{ $classBaseName }}FormFields(): JSX.Element {
  return (
    <div className="{{ \Str::kebab(lcfirst($classBaseName)) }}-form-fields-root">
@foreach( $columnsForRender as $c )
    @if( $c->editable() )
        @if( $c->getCastType() === 'boolean' )
            <AppFormRadio
                name="{{ $c->getNameAsRequestKey() }}"
                label="{{ $c->getLabel() }}"
                options={[
                    { label: 'はい', value: '1' },
                    { label: 'いいえ', value: '0' },
                ]}
            />
        @elseif( str_contains($c->column->getComment() ?? '', '@enum:') )
            @php
                preg_match('/@enum:\[(.*)\]/', $c->column->getComment(), $matches);
                $enumOptions = array_map(
                    static fn($item)=> array_map('trim', explode(":", $item)),
                    explode(",", $matches[1])
                );
            @endphp
            <AppFormSelect
                name="{{ $c->getNameAsRequestKey() }}"
                label="{{ $c->getLabel() }}"
                options={[
                @foreach($enumOptions as $option)
                    {label: '{!! $option[1] !!}',  value: '{!! $option[0] !!}',},
                @endforeach
                ]}
            />
        @else
            <AppFormTextField name="{{ $c->getNameAsRequestKey() }}" label="{{ $c->getLabel() }}" />
        @endif
    @endif
@endforeach
    </div>
  );
}

export { {{ $classBaseName }}FormFields, to{{ $classBaseName }}FormInputTypes };
export type { {{ $classBaseName }}FormInputTypes };
