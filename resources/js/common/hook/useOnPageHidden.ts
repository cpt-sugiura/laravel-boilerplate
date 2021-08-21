import { useEffect } from 'react';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export const useOnPageHidden = (callBack: () => void): void => {
  useEffect(() => {
    // todo ページが閉じた時、再度開いた時に再開する機能を追加
    // let visibilityChange;
    // if (typeof document.hidden !== 'undefined') {
    //   visibilityChange = 'visibilitychange';
    // } else if (typeof document.mozHidden !== 'undefined') {
    //   visibilityChange = 'mozvisibilitychange';
    // } else if (typeof document.msHidden !== 'undefined') {
    //   visibilityChange = 'msvisibilitychange';
    // } else if (typeof document.webkitHidden !== 'undefined') {
    //   visibilityChange = 'webkitvisibilitychange';
    // }
    // document.addEventListener(visibilityChange, () => {
    //   // ページ非表示と表示時に呼ばれるため、表示から非表示になった時だけ呼ばれるように
    //   if (document.hidden) {
    //     callBack();
    //   }
    // });
    //
    // window.addEventListener('pagehide', callBack);
  }, []);
};
