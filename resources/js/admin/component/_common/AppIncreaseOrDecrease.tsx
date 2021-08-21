import * as React from 'react';
import Fab from '@material-ui/core/Fab';
import CloseIcon from '@material-ui/icons/Close';
import AddIcon from '@material-ui/icons/Add';
import Grid from '@material-ui/core/Grid';
import { CSSProperties, PropsWithChildren } from 'react';

type AppIncreaseOrDecreasePaperProps<T> = {
  index: number;
  items: T[];
  itemInitParamCreateFn: () => T;
  onChangeItems: (items: T[]) => void;
  onClickClose?: () => void;
  onClickAdd?: () => void;
};

/**
 * 増減する要素を作る
 */
export function AppIncreaseOrDecrease<T>(props: PropsWithChildren<AppIncreaseOrDecreasePaperProps<T>>) {
  const toggleDisplayStyle = (isDisplay: boolean): CSSProperties => {
    return {
      visibility: isDisplay ? 'visible' : 'hidden',
      order: isDisplay ? 1 : -1,
    };
  };
  const items = props.items;
  const isFirstItem = props.index === 0;
  const isLastItem = props.index === props.items.length - 1;
  const onClickAdd = () => {
    items.push(props.itemInitParamCreateFn());
    props.onChangeItems(items);
  };
  const onClickClose = () => {
    const newItems = items.filter((item, index) => `${index}` !== `${props.index}`);
    props.onChangeItems(newItems);
  };

  return (
    <Grid container>
      <Grid item xs={10}>
        {props.children}
      </Grid>
      <Grid item xs={2}>
        <div
          style={{
            display: 'flex',
            justifyContent: 'flex-end',
            width: '100%',
            height: '100%',
            paddingRight: '10px',
          }}
        >
          <Fab style={toggleDisplayStyle(!isFirstItem)} onClick={props.onClickClose || onClickClose}>
            <CloseIcon />
          </Fab>
          <Fab color="primary" style={toggleDisplayStyle(isLastItem)} onClick={props.onClickAdd || onClickAdd}>
            <AddIcon />
          </Fab>
        </div>
      </Grid>
    </Grid>
  );
}
