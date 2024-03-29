import React, { Fragment, useState } from 'react';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Typography from '@mui/material/Typography';
import CardActions from '@mui/material/CardActions';
import CircularProgress from '@mui/material/CircularProgress';
import Popper, { PopperProps } from '@mui/material/Popper';
import Button, { ButtonProps } from '@mui/material/Button';
import { errorColor } from '@/admin/theme';

type DeleteBtnProps = {
  isDeleting?: boolean;
  label?: string;
  deleteAction?: () => void;
  ButtonProps?: ButtonProps;
};

const DeleteBtn = (props: DeleteBtnProps) => {
  const [deletePopAnchor, setDeletePopAnchor] = useState<PopperProps['anchorEl']>(null);
  return (
    <Fragment>
      <Button
        className="delete-btn"
        {...props.ButtonProps}
        style={{
          backgroundColor: errorColor.dark,
          color: errorColor.contrastText,
        }}
        onClick={(e) => setDeletePopAnchor(e.currentTarget)}
      >
        {props.label || '削除'}
      </Button>
      <Popper open={!!deletePopAnchor} anchorEl={deletePopAnchor} placement="top-start" style={{ zIndex: 3 }}>
        <Card elevation={3}>
          <CardContent>
            <Typography variant="body2" component="p">
              本当に削除しますか？
            </Typography>
          </CardContent>
          <CardActions>
            <Button className="back-btn" onClick={() => setDeletePopAnchor(null)}>
              取消
            </Button>
            <Button
              style={{
                backgroundColor: errorColor.dark,
                color: errorColor.contrastText,
              }}
              onClick={props.deleteAction}
            >
              {props.isDeleting ? <CircularProgress color="primary" /> : '削除'}
            </Button>
          </CardActions>
        </Card>
      </Popper>
    </Fragment>
  );
};

export { DeleteBtn };
