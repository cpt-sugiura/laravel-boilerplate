import React, { PropsWithChildren, useContext, useState } from 'react';
import { AppDialog } from '@/common/component/AppDialog';
import { ResponseMessage } from '@/common/hook/makeUseAxios';
import { ResponseMessageTypography } from '@/common/component/ResponseMessageTypography';

type setDialogFunc = (title: string, bodyComponent: JSX.Element, actionComponent?: JSX.Element) => void;
type setDialogFromResponseMsgFunc = (title: string, responseMsg: ResponseMessage) => void;
type setOpenFunc = (open: boolean) => void;
type DialogMessageContextRet = {
  setDialog: setDialogFunc;
  setDialogFromResponseMsg: setDialogFromResponseMsgFunc;
  setOpen: setOpenFunc;
};
export const DialogMessagesContext = React.createContext<DialogMessageContextRet>({
  // eslint-disable-next-line @typescript-eslint/no-empty-function,@typescript-eslint/no-unused-vars
  setDialog: (title: string, bodyComponent: JSX.Element, actionComponent?: JSX.Element): void => {},
  // eslint-disable-next-line @typescript-eslint/no-empty-function,@typescript-eslint/no-unused-vars
  setDialogFromResponseMsg: (title: string, responseMsg: ResponseMessage): void => {},
  // eslint-disable-next-line @typescript-eslint/no-empty-function,@typescript-eslint/no-unused-vars
  setOpen: (open: boolean) => {},
});

/**
 * ダイアログに送られてきたメッセージを表示する
 * @constructor
 */
const DialogMessagesComponent: React.FC<PropsWithChildren<{}>> = ({ children }) => {
  const [open, setOpen] = useState(false);
  const [title, setTitle] = useState('');
  const [bodyComponent, setBodyComponent] = useState<JSX.Element>(<div />);
  const [actionComponent, setActionComponent] = useState<JSX.Element>(<div />);
  const setDialog = (newTitle: string, newBodyComponent: JSX.Element, newActionComponent?: JSX.Element) => {
    setOpen(true);
    setTitle(newTitle);
    setBodyComponent(newBodyComponent);
    newActionComponent && setActionComponent(newActionComponent);
  };
  const setDialogFromResponseMsg = (newTitle: string, responseMsg: ResponseMessage) => {
    setOpen(true);
    setTitle(newTitle);
    setBodyComponent(<ResponseMessageTypography msg={responseMsg} />);
  };
  return (
    <DialogMessagesContext.Provider value={{ setDialog, setOpen, setDialogFromResponseMsg }}>
      <AppDialog title={title} isOpen={open} setIsOpen={setOpen} DialogActionsComponent={actionComponent}>
        {bodyComponent}
      </AppDialog>
      {children}
    </DialogMessagesContext.Provider>
  );
};

// 各関数コンポーネントで使う
const useMessageDialog = (): DialogMessageContextRet => useContext(DialogMessagesContext);
// ルートコンポーネントで使う
const DialogMessagesProvider = DialogMessagesComponent;

export { useMessageDialog, DialogMessagesProvider };
