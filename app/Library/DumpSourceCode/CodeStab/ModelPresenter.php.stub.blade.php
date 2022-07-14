%%php%%

namespace App\Http\Controllers\{{ $domain }}BrowserAPI\Presenters;

use App\Http\Presenters\BasePresenter;
use {{ $classFullName }};

class {{ $classBaseName }}Presenter extends BasePresenter
{
    public function __construct(private {{ $classBaseName }} ${{ \Str::camel($classBaseName) }})
    {
    }

    public function toArray(): array
    {
@php
$foreignColumns = [];
/** @var \App\Library\DumpSourceCode\ColumnForRender[] $columnsForRender */
foreach( $columnsForRender as $col){
    /** @var string $tableName */
    if( $col->model->getTable() !== $tableName ){
        $foreignColumns[] = $col;
    }
}
@endphp

@if( empty($foreignColumns) )
        return $this->{{ \Str::camel($classBaseName) }}->toArray();
@else
        return array_merge(
            $this->{{ \Str::camel($classBaseName) }}->toArray(),
            [
    @foreach( $foreignColumns as $col )
                "{{ $col->getNameAsRequestKey() }}" => $this->{{ \Str::camel($classBaseName) }}->{{ lcfirst(\Str::camel(\Str::singular($col->model->getTable()))) }}->{{ $col->column->getName() }},
    @endforeach
            ]
        );
@endif
    }
}
