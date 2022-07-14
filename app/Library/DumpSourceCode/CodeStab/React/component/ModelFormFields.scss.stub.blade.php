@import "resources/sass/{{ lcfirst($domain) }}/variables";
.{{ \Str::kebab(lcfirst($classBaseName)) }}-form-fields-root {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: ${{ lcfirst($domain) }}-base-gap-size;
    grid-template-areas:
@php
$resolver = fn(\Doctrine\DBAL\Schema\Column|null $column) => $column !== null ? \Str::camel($column->getName()) : '.';
$lines = [];
/** @var \App\Library\DumpSourceCode\ColumnForRender[] $columnsForRender */
$editableColumns = array_values(array_filter($columnsForRender, fn(\App\Library\DumpSourceCode\ColumnForRender $c) => $c->editable()));
$len = count($editableColumns);
for($i = 0; $i < $len; $i += 4){
    $cellList = [];
    for($j = 0; $j < 4; $j++){
        $cellList[] = isset($editableColumns[$i+$j]) ? $editableColumns[$i+$j]->getNameAsRequestKey() : '.';
    }
    $lines[] = '"'.implode(' ', $cellList).'"';
}
@endphp
@foreach( $lines as $l)
    {!! $l  !!}
@endforeach
    ;
    padding-bottom: $search-box-gap-size;

@foreach( $columnsForRender as $c )
    @if( $c->editable() )
    .{{ $c->getNameAsRequestKey() }} { grid-area: {{ $c->getNameAsRequestKey() }}; }
    @endif
@endforeach
}
