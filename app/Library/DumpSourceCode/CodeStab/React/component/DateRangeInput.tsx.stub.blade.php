<AppDateRangeInput
    RootBoxProps={!! "{"."{"."className: '". $name.  "',"."}"."}" !!}
    startLabel={'{{ $label  }}'}
    startName={'{{ $startName }}'}
    emitStartDate={updateState}
    startDefaultValue={searchBox.{{ $startName }}}
    endLabel={''}
    endName={'{{ $endName }}'}
    emitEndDate={updateState}
    endDefaultValue={searchBox.{{ $endName }}}
    resetTriggerVal={clearSearchTrigger}
/>
