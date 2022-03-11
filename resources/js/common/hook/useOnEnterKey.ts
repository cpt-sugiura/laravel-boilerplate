import { KeyboardEventHandler, KeyboardEvent } from 'react';

type KeyBoardHandler<T> = KeyboardEventHandler<T>;

export const useOnEnterKey = <T>(
  func: (e?: KeyboardEvent<T>) => void
): {
  onEnterKey: KeyBoardHandler<T>;
} => {
  const onEnterKey: KeyboardEventHandler<T> = (event): void => {
    // event.key のみでは日本語変換時の Enter が暴発する
    // todo keyCode は非推奨なレガシー API なので代替があればそちらに移行
    if (event.key === 'Enter' && event.keyCode === 13) {
      func(event);
    }
  };
  return {
    onEnterKey,
  };
};
