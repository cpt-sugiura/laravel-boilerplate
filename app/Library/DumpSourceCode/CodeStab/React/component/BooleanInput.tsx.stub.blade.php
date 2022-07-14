<AppRadio
  name="{{ $name }}"
  label="{{ $label }}"
  options={[
    { label: '未選択', value: '' },
    { label: 'はい', value: '1' },
    { label: 'いいえ', value: '0' },
  ]}
  onChange={(v) => updateState({target: {name: '{{ $name }}', value: v,},})}
/>
