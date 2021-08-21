import React, { SyntheticEvent } from 'react';
import Icon from '@material-ui/core/Icon';

export const TriangleIcon = React.memo(TriangleIconComponent);
type Props = {
  onClick?: (event: SyntheticEvent) => void;
};

/**
 * 一手前に戻るアイコン
 * @constructor
 */
function TriangleIconComponent(props: Props): JSX.Element {
  return (
    <Icon onClick={props.onClick}>
      <svg
        className="MuiSvgIcon-root MuiSelect-icon MuiSelect-iconOutlined"
        focusable="false"
        viewBox="0 0 24 24"
        aria-hidden="true"
      >
        <path d="M7 10l5 5 5-5z" />
      </svg>
    </Icon>
  );
}
