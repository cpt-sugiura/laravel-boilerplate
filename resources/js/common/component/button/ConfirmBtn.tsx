import React, { Fragment, useState } from 'react';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Typography from '@mui/material/Typography';
import CardActions from '@mui/material/CardActions';
import CircularProgress from '@mui/material/CircularProgress';
import Popper, { PopperProps } from '@mui/material/Popper';
import Button, { ButtonProps } from '@mui/material/Button';

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
            <Button onClick={() => setConfirmPopAnchor(null)} className="back-btn">
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
