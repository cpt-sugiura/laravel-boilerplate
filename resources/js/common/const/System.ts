export type SelectOption<V = number | string> = {
  label: string;
  value: V;
  selected?: boolean;
};
