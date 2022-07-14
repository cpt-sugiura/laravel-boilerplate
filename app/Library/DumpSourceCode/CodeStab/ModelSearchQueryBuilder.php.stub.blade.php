%%php%%

namespace App\Models\Search\{{ $domain }}API\{{ $classBaseName }};

use App\Models\Search\BaseQueryBuilder\BaseSearchQueryBuilder;
use App\Models\Search\BaseQueryBuilder\Macros\SearchWhereMacro;
use DB;
use Illuminate\Database\Query\Builder;

class {{ $classBaseName }}SearchQueryBuilder extends BaseSearchQueryBuilder
{
    public function select(): array
    {
        return [
@foreach( $columnsForRender as $c)
    @if( $c->visible() )
            '{{ $c->getFullNameInDB() }}',
    @endif
@endforeach
        ];
    }

    /**
     * @return Builder
     */
    protected function from(): Builder
    {
        return DB::query()->from('{{ $tableName }}')
@foreach( $searchJoinList as $j)
            ->{{ $j[0] }}('{!! implode("','", array_slice($j, 1)) !!}')
@endforeach
@if( $hasDeletedAt )
            ->whereNull('{{ $tableName }}.deleted_at');
@else
    ;
@endif
    }

    protected function searchableWhereFields(): array
    {
        return [
@foreach( $searchMethods as $method)
            '{!! $method['name'] !!}' => {!! $method['method'] !!},
@endforeach
        ];
    }

    protected function orderByAbleFields(): array
    {
        return [
@foreach( $columnsForRender as $c)
    @if( $c->visible() )
            '{{ $c->getNameAsRequestKey() }}' => '{{ $c->getFullNameInDB() }}',
    @endif
@endforeach
        ];
    }
}
