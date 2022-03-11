import React, { PropsWithChildren } from 'react';
import Dialog from '@mui/material/Dialog';
import { ModalProps } from '@mui/material/Modal';
import Button from '@mui/material/Button';
import Typography from '@mui/material/Typography';
import IconButton from '@mui/material/IconButton';
import CloseIcon from '@mui/icons-material/Close';
import MuiDialogTitle from '@mui/material/DialogTitle';
import { useTrans } from '@/lang/useLangMsg';
import {DialogActions, DialogContent} from "@mui/material";

export const AppDialog = React.memo(AppDialogComponent);

type DialogTitleProps = {
  children: React.ReactNode;
  close: () => void;
}

const DialogTitle = (props: DialogTitleProps) => {
  const { children, close, ...other } = props;
  return (
    <MuiDialogTitle className={'dialog-title'} {...other}>
      <Typography variant="h6">{children}</Typography>
      <IconButton aria-label="close" className="close-btn" onClick={close}>
        <CloseIcon />
      </IconButton>
    </MuiDialogTitle>
  );
};

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
    <Dialog onClose={onClose} open={isOpen} className="app-dialog">
      <DialogTitle close={() => setIsOpen(false)}>{title}</DialogTitle>
      <DialogContent className={"dialog-content"} dividers>{props.children}</DialogContent>
      <DialogActions className={"dialog-actions"}>
        {DialogActionsComponent ? (
          DialogActionsComponent
        ) : (
          <Button onClick={() => setIsOpen(false)} className="back-btn">
            {t('app.close')}
          </Button>
        )}
      </DialogActions>
    </Dialog>
  );
}
