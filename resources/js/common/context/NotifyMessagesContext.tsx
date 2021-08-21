import React, { CSSProperties, PropsWithChildren, useContext, useState } from 'react';
import Snackbar from '@material-ui/core/Snackbar';
import IconButton from '@material-ui/core/IconButton';
import CloseIcon from '@material-ui/icons/Close';
export type NotifyMessagesContextType = {
  messages: { [key: string]: string };
  pushMessage: (msg: string, style?: CSSProperties) => void;
};
export const NotifyMessagesContext = React.createContext<NotifyMessagesContextType>({
  messages: {},
  pushMessage: (msg: string): void => {
    console.error('NotifyMessagesContext is defaultValue', msg);
  },
});

// 各関数コンポーネントで使う
export const useNotifyMessages = (): NotifyMessagesContextType => useContext(NotifyMessagesContext);
// ルートコンポーネントで使う
export const NotifyMessagesProvider = NotifyMessagesComponent;

let msgIndex = 0;
/**
 * スナックバーに送られてきたメッセージを表示する
 * @constructor
 */
function NotifyMessagesComponent(props: PropsWithChildren<{}>): JSX.Element {
  const [messages, setMessages] = useState<{ [key: string]: string }>({});
  const [styles, setStyles] = useState<{ [key: string]: CSSProperties }>({});

  const pushMessage = (newMessage: string, style?: CSSProperties) => {
    const newMsgIndex = msgIndex + 1;
    setMessages({ ...messages, [`${newMsgIndex}`]: newMessage });
    setStyles({ ...styles, [`${newMsgIndex}`]: style || {} });
    msgIndex++;
  };
  const deleteMessage = (deleteKey: string, reason = '') => {
    if (reason === 'clickaway') {
      // スナックバーの外をクリックした時は閉じない
      return;
    }
    const newMessages: { [key: string]: string } = {};
    Object.keys(messages)
      .filter((msgKey) => msgKey !== deleteKey)
      .forEach((msgKey) => (newMessages[msgKey] = messages[msgKey]));
    setMessages(newMessages);
  };

  return (
    <NotifyMessagesContext.Provider value={{ messages, pushMessage }}>
      {Object.keys(messages).map((msgKey, index) => (
        <Snackbar
          key={msgKey}
          anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
          style={{ bottom: `${24 + 72 * index}px`, ...styles[msgKey] }}
          autoHideDuration={5000}
          open={!!messages[msgKey]}
          onClose={(event, reason) => deleteMessage(msgKey, reason)}
          message={messages[msgKey]}
          action={
            <IconButton size="small" aria-label="close" color="inherit" onClick={() => deleteMessage(msgKey)}>
              <CloseIcon fontSize="small" />
            </IconButton>
          }
        />
      ))}
      {props.children}
    </NotifyMessagesContext.Provider>
  );
}
