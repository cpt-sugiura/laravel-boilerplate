import React, { Fragment, useState } from 'react';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Typography from '@mui/material/Typography';
import CardActions from '@mui/material/CardActions';
import CircularProgress from '@mui/material/CircularProgress';
import Popper, { PopperProps } from '@mui/material/Popper';
import Button, { ButtonProps } from '@mui/material/Button';
import { useTrans } from '@/lang/useLangMsg';

type DeleteBtnProps = {
  isDeleting?: boolean;
  label?: string;
  deleteAction?: () => void;
  deleteActionPromise?: () => Promise<void>;
  ButtonProps?: ButtonProps;
};

const DeleteBtn: React.FC<DeleteBtnProps> = (props) => {
  const t = useTrans();
  const [deletePopAnchor, setDeletePopAnchor] = useState<PopperProps['anchorEl']>(null);
  const runDeleteAction = () => {
    if (props.deleteAction) {
      props.deleteAction();
      setDeletePopAnchor(null);
    } else if (props.deleteActionPromise) {
      const prePerformHref = window.location.href;
      props.deleteActionPromise().finally(() => {
        // アンマウント済みコンポーネントへの state update 対策
        if (prePerformHref === window.location.href) {
          setDeletePopAnchor(null);
        }
      });
    }
  };
  return (
    <Fragment>
      <Button
        className="delete-btn"
        {...props.ButtonProps}
        color={'secondary'}
        onClick={(e) => setDeletePopAnchor(e.currentTarget)}
      >
        {props.label || t('app.delete.btn')}
      </Button>
      <Popper open={!!deletePopAnchor} anchorEl={deletePopAnchor} placement="top-start" style={{ zIndex: 100000 }}>
        <Card elevation={3}>
          <CardContent>
            <Typography variant="body2" component="p">
              {t('app.delete.confirm.msg')}
            </Typography>
          </CardContent>
          <CardActions>
            {/* todo color */}
            <Button onClick={() => setDeletePopAnchor(null)} className="back-btn">
              {t('app.delete.cancel')}
            </Button>
            <Button color="secondary" onClick={runDeleteAction}>
              {props.isDeleting ? <CircularProgress color="primary" /> : t('app.delete.btn')}
            </Button>
          </CardActions>
        </Card>
      </Popper>
    </Fragment>
  );
};

export { DeleteBtn };
