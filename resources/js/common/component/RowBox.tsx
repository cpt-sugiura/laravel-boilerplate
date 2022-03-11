import React from 'react';
import Box, { BoxProps } from '@mui/material/Box';

export const RowBox = React.memo(RowBoxComponent);

/**
 * 行のBox
 * @param props
 * @constructor
 */
function RowBoxComponent(props: BoxProps): JSX.Element {
  return (
    <Box {...props} className={`row ${props.className}`}>
      {props.children}
    </Box>
  );
}
