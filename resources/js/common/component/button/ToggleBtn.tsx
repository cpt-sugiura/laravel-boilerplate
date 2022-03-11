import React, { CSSProperties } from 'react';
import Button from '@mui/material/Button';

type ToggleBtnProps = {
  label: string;
  active: boolean;
  emitActive?: (newVal: boolean) => void;
};
const activeStyle: CSSProperties = {
  background: 'rgba(0, 75, 0, 1)',
};

export const ToggleBtn: React.FC<ToggleBtnProps> = (props) => {
  const handleClick = () => {
    props.emitActive && props.emitActive(!props.active);
  };

  return (
    <Button color="primary" style={props.active ? activeStyle : {}} onClick={handleClick}>
      {props.label}
    </Button>
  );
};
