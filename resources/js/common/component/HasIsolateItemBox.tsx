import React, { CSSProperties, PropsWithChildren } from 'react';
import Box, { BoxProps } from '@material-ui/core/Box';

type HasIsolateItemBoxProps = {
  BoxProps?: BoxProps;
  innerStyle?: CSSProperties;
  justifyContent?: CSSProperties['justifyContent'];
};
/**
 * ポツンとある孤立した要素を持つための箱
 * @param props
 */
export const HasIsolateItemBox: React.FC<PropsWithChildren<HasIsolateItemBoxProps>> = (props) => {
  return (
    <Box
      {...props.BoxProps}
      className={`row ${props?.BoxProps?.className || ''}`}
      style={{ justifyContent: props.justifyContent || 'flex-end', ...props?.BoxProps?.style }}
    >
      {props.children && Array.isArray(props.children) ? (
        props.children.map((c, i) => (
          <div
            key={i}
            style={{
              height: '2em',
              ...props.innerStyle,
            }}
          >
            {c}
          </div>
        ))
      ) : (
        <div
          style={{
            height: '2em',
            ...props.innerStyle,
          }}
        >
          {props.children}
        </div>
      )}
    </Box>
  );
};
