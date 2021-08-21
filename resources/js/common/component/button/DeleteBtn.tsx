import React, { Fragment, useState } from 'react';
import Card from '@material-ui/core/Card';
import CardContent from '@material-ui/core/CardContent';
import Typography from '@material-ui/core/Typography';
import CardActions from '@material-ui/core/CardActions';
import CircularProgress from '@material-ui/core/CircularProgress';
import Popper, { PopperProps } from '@material-ui/core/Popper';
import Button, { ButtonProps } from '@material-ui/core/Button';
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
            <Button onClick={() => setDeletePopAnchor(null)} color="default">
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
