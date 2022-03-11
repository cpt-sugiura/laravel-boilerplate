import React, { PropsWithChildren } from 'react';
import Box, { BoxProps as OriginBoxProps } from '@mui/material/Box';

export const ColBox = React.memo(ColBoxComponent);

type ColBoxProps = OriginBoxProps & {
  BoxProps?: OriginBoxProps;
  withSpace?: boolean;
  className?: string;
};

/**
 * 列のBox
 */
function ColBoxComponent(props: PropsWithChildren<ColBoxProps>): JSX.Element {
  const { BoxProps, withSpace, className, ...onlyBoxProps } = props;
  return (
    <Box className={`col ${withSpace ? 'space' : ''} ${className || ''}`} {...onlyBoxProps} {...BoxProps}>
      {props.children}
    </Box>
  );
}
