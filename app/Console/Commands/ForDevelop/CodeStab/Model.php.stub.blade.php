%%php%%

namespace {{ $namespace }};

use App\Models\Eloquents\BaseEloquent as Model;

class {{ $className }} extends Model
{
    public $table = '{{ $tableName }}';
@if( is_string($primaryKey) )
    protected $primaryKey = '{{ $primaryKey }}';
@elseif( is_array($primaryKey) )
    protected $primaryKey = [
@foreach( $primaryKey as $p)
        '{{ $p }}',
@endforeach
    ];
@endif

@if( !empty($hidden) )
    protected $hidden = [
@foreach( $hidden as $h)
        '{{ $h }}',
@endforeach
    ];
@endif

    public $guarded = [
@foreach( $guarded as $g)
        '{{ $g }}',
@endforeach
    ];

    protected $casts =[
@foreach( $casts as $k => $v)
@if( $v === null )
        // WARN: skip {{ $k }}
@else
        '{{ $k }}' => '{{ $v }}',
@endif
@endforeach
    ];

@php
/** @var array{retClassName: string, useMethod: string, keyColumnPHPCode: string, methodName: string, tgtClassNamePHPCode: string } $rel */
@endphp
@foreach( $relations as $rel)
    public function {{ $rel['methodName'] }}(): {{ $rel['retClassName'] }}
    {
        return $this->{{ $rel['useMethod'] }}({!! $rel['tgtClassNamePHPCode'] !!}, {!! $rel['keyColumnPHPCode'] !!});
    }
@endforeach
}
