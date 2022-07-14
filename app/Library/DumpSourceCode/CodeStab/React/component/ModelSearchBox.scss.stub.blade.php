@import "resources/sass/{{ $domain }}/variables";

.{{ \Str::kebab(lcfirst($classBaseName)) }}-search-box {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: $search-box-gap-size;
    grid-template-areas:
@php
    $searchMethodsForReactLen = count($searchMethodsForReact);
    /** @var \App\Models\Eloquents\BaseEloquent $classFullName */
    $hasTimestamp = (new $classFullName)->usesTimestamps();
    for($searchMethodsForReactIndex = 0; $searchMethodsForReactIndex < $searchMethodsForReactLen; ){
        $line = [];
        for($cellIndex = 0; $cellIndex < 4;  ){
            $i = $searchMethodsForReactIndex;// 変数名が長くて見難いので短縮
            $gridSpace = $searchMethodsForReact[$i]['gridSpace'] ?? 1;
            if($cellIndex + $gridSpace > 4){
                echo '"'.implode(' ', $line).' '.str_repeat('. ', 4 - count($line))."\"\n";
                continue 2;
            }
            for($k = 0; $k < $gridSpace; $k++){
                if(
                    isset($searchMethodsForReact[$i]['name'])
                     && (!$hasTimestamp || !in_array($searchMethodsForReact[$i]['name'],['createdAt','updatedAt'],true))
                ){
                    $line[] = $searchMethodsForReact[$i]['name'];
                } else {
                    $line[] = '.';
                }
                $cellIndex++;
            }
            $searchMethodsForReactIndex++;
        }
        if(implode(' ', $line) !== '. . . .'){
            echo '"'.implode(' ', $line)."\"\n";
        }
    }
    if($hasTimestamp){
        echo '"createdAt createdAt updatedAt updatedAt"'."\n";
    }
@endphp
    "control control control control";

    .control {
        grid-area: control;
        display: flex;
        flex-direction: row;
        justify-content: center;
        button {
            width: fit-content;
        }

        :not(:last-child) {
            margin-right: $search-box-gap-size;
        }

    }

@foreach( $searchMethodsForReact as $method)
    .{{ $method['name'] }} { grid-area: {{ $method['name'] }}; }
@endforeach

}
