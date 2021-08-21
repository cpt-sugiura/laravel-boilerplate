import React, { PropsWithChildren } from 'react';
import Dialog from '@material-ui/core/Dialog';
import { ModalProps } from '@material-ui/core/Modal';
import Button from '@material-ui/core/Button';
import { createStyles, Theme, withStyles, WithStyles } from '@material-ui/core/styles';
import Typography from '@material-ui/core/Typography';
import IconButton from '@material-ui/core/IconButton';
import CloseIcon from '@material-ui/icons/Close';
import MuiDialogTitle from '@material-ui/core/DialogTitle';
import MuiDialogContent from '@material-ui/core/DialogContent';
import MuiDialogActions from '@material-ui/core/DialogActions';
import { useTrans } from '@/lang/useLangMsg';

export const AppDialog = React.memo(AppDialogComponent);

const titleStyles = (theme: Theme) =>
  createStyles({
    root: {
      margin: 0,
      padding: theme.spacing(2),
      minWidth: '25vw',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'space-between',
    },
    closeButton: {
      color: theme.palette.grey[500],
      padding: 0,
    },
  });

interface DialogTitleProps extends WithStyles<typeof titleStyles> {
  children: React.ReactNode;
  close: () => void;
}

const DialogTitle = withStyles(titleStyles)((props: DialogTitleProps) => {
  const { children, classes, close, ...other } = props;
  return (
    <MuiDialogTitle disableTypography className={classes.root} {...other}>
      <Typography variant="h6">{children}</Typography>
      <IconButton aria-label="close" className={classes.closeButton} onClick={close}>
        <CloseIcon />
      </IconButton>
    </MuiDialogTitle>
  );
});

const DialogContent = withStyles((theme: Theme) => ({
  root: {
    padding: theme.spacing(2),
  },
}))(MuiDialogContent);

const DialogActions = withStyles((theme: Theme) => ({
  root: {
    margin: 0,
    padding: theme.spacing(1),
  },
}))(MuiDialogActions);

type AppDialogProps = {
  title: string;
  isOpen: boolean;
  setIsOpen: (newIsOpen: boolean) => void;
  onClose?: ModalProps['onClose'];
  DialogActionsComponent?: JSX.Element;
};

/**
 * ダイアログ
 */
function AppDialogComponent(props: PropsWithChildren<AppDialogProps>): JSX.Element {
  const { title, onClose, isOpen, setIsOpen, DialogActionsComponent } = props;
  const t = useTrans();
  return (
    <Dialog onClose={onClose} open={isOpen}>
      <DialogTitle close={() => setIsOpen(false)}>{title}</DialogTitle>
      <DialogContent dividers>{props.children}</DialogContent>
      <DialogActions>
        {DialogActionsComponent ? (
          DialogActionsComponent
        ) : (
          <Button onClick={() => setIsOpen(false)} color="default">
            {t('app.close')}
          </Button>
        )}
      </DialogActions>
    </Dialog>
  );
}
