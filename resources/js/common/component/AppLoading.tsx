import CircularProgress from '@material-ui/core/CircularProgress';
import React, { CSSProperties } from 'react';
import { useTrans } from '@/lang/useLangMsg';

export const AppLoading = React.memo(AppLoadingComponent);

/**
 * ある Element いっぱいに広がるローディング画面
 * @param props
 * @constructor
 */
function AppLoadingComponent(props: {
  message?: string | JSX.Element;
  isOverlay?: boolean;
  processingPer?: number;
  withoutBackground?: boolean;
  inline?: boolean;
  circleColor?: 'primary' | 'secondary' | 'inherit';
  progressStyle?: CSSProperties;
}): JSX.Element {
  const t = useTrans();

  const overlayStyle: CSSProperties = props.isOverlay
    ? {
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        background: 'rgba(255, 255, 255, 0.75)',
      }
    : {};
  const itemStyle: CSSProperties = {
    marginLeft: '.5em',
    marginRight: '.5em',
  };
  const circleStyle: CSSProperties = props.inline
    ? {
        width: '1.75em',
        height: '1.75em',
      }
    : {
        width: '40px',
        height: '40px',
      };

  const backgroundBaseStyle: CSSProperties = {
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    height: '100%',
  };
  return (
    <div
      className={props.withoutBackground ? '' : 'MuiBackdrop-root'}
      style={
        props.withoutBackground
          ? backgroundBaseStyle
          : {
              ...backgroundBaseStyle,
              transition: 'opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms',
              background: 'rgba(255, 255, 255, 0.25)',
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
              height: '100%',
              ...overlayStyle,
            }
      }
    >
      <div style={itemStyle}>{props.message || t('app.loading')}</div>
      {props.processingPer ? <div style={itemStyle}>{props.processingPer.toFixed(2)}%</div> : ''}
      <CircularProgress
        style={{ ...itemStyle, ...circleStyle, ...props.progressStyle }}
        color={props.circleColor || 'primary'}
      />
    </div>
  );
}
