import {
  ContextMenu as ContextMenuOrigin,
  ContextMenuProps,
  ContextMenuTrigger as ContextMenuTriggerOrigin,
  ContextMenuTriggerProps,
} from 'react-contextmenu';
import React, { PropsWithChildren } from 'react';
import ClickAwayListener from '@material-ui/core/ClickAwayListener';

/**
 * 右クリックメニュー用コンポーネント生成器
 */
export function contextMenuProvider(id: string | null = null): {
  ContextMenuTrigger: React.FC<PropsWithChildren<Partial<ContextMenuTriggerProps>>>;
  ContextMenuBody: React.FC<PropsWithChildren<Partial<ContextMenuProps>>>;
} {
  const contextId: string = id || `${Math.random()}`;
  const ContextMenuTrigger = (props: PropsWithChildren<Partial<ContextMenuTriggerProps>>): JSX.Element => (
    <ContextMenuTriggerOrigin id={contextId} {...props}>
      {props.children}
    </ContextMenuTriggerOrigin>
  );
  const ContextMenuBody = (props: PropsWithChildren<Partial<ContextMenuProps>>): JSX.Element => {
    const [open, setOpen] = React.useState<boolean>(false);
    const handleClickAway = () => {
      setOpen(false);
    };
    return (
      <ClickAwayListener onClickAway={handleClickAway}>
        <ContextMenuOrigin
          id={contextId}
          {...props}
          onShow={() => setOpen(true)}
          style={{
            zIndex: 1e10,
            display: open ? 'block' : 'none',
          }}
        >
          {props.children}
        </ContextMenuOrigin>
      </ClickAwayListener>
    );
  };

  return { ContextMenuTrigger: React.memo(ContextMenuTrigger), ContextMenuBody: React.memo(ContextMenuBody) };
}
