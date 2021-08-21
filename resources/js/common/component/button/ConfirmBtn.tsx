import React, { Fragment, useState } from 'react';
import Card from '@material-ui/core/Card';
import CardContent from '@material-ui/core/CardContent';
import Typography from '@material-ui/core/Typography';
import CardActions from '@material-ui/core/CardActions';
import CircularProgress from '@material-ui/core/CircularProgress';
import Popper, { PopperProps } from '@material-ui/core/Popper';
import Button, { ButtonProps } from '@material-ui/core/Button';

type ConfirmBtnProps = {
  isExecuting?: boolean;
  label: string;
  execAction?: () => void;
  execActionPromise?: () => Promise<void>;
  ButtonProps?: ButtonProps;
  confirmMsg: string;
  confirmCancelBtnLabel: string;
  confirmActionBtnLabel: string;
};

const ConfirmBtn: React.FC<ConfirmBtnProps> = (props) => {
  const [confirmPopAnchor, setConfirmPopAnchor] = useState<PopperProps['anchorEl']>(null);
  const runConfirmAction = () => {
    if (props.execAction) {
      props.execAction();
      setConfirmPopAnchor(null);
    } else if (props.execActionPromise) {
      const prePerformHref = window.location.href;
      props.execActionPromise().finally(() => {
        // アンマウント済みコンポーネントへの state update 対策
        if (prePerformHref === window.location.href) {
          setConfirmPopAnchor(null);
        }
      });
    }
  };
  return (
    <Fragment>
      <Button
        className="confirm-btn"
        {...props.ButtonProps}
        color={'secondary'}
        onClick={(e) => setConfirmPopAnchor(e.currentTarget)}
      >
        {props.label}
      </Button>
      <Popper open={!!confirmPopAnchor} anchorEl={confirmPopAnchor} placement="top-start" style={{ zIndex: 100000 }}>
        <Card elevation={3}>
          <CardContent>
            <Typography variant="body2" component="p">
              {props.confirmMsg}
            </Typography>
          </CardContent>
          <CardActions>
            <Button onClick={() => setConfirmPopAnchor(null)} color="default">
              {props.confirmCancelBtnLabel}
            </Button>
            <Button color="secondary" onClick={runConfirmAction}>
              {props.isExecuting ? <CircularProgress color="primary" /> : props.confirmActionBtnLabel}
            </Button>
          </CardActions>
        </Card>
      </Popper>
    </Fragment>
  );
};

export { ConfirmBtn };
