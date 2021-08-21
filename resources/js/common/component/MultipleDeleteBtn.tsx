import React, { useState } from 'react';
import Button, { ButtonProps } from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import DialogTitle from '@material-ui/core/DialogTitle';
import DialogContent from '@material-ui/core/DialogContent';
import DialogActions from '@material-ui/core/DialogActions';
import { useTrans } from '@/lang/useLangMsg';
import { AppLoading } from '@/common/component/AppLoading';

type MultipleDeleteBtnProps = {
  deleteAction: () => void;
  ButtonProps?: ButtonProps;
  open?: boolean;
  isLoading?: boolean;
  openAction?: () => void;
  closeAction?: () => void;
};

export const MultipleDeleteBtn: React.FC<MultipleDeleteBtnProps> = (props) => {
  const [open, setOpen] = useState(!!props.open);

  const handleOpenAction = () => {
    typeof props.openAction === 'function' && props.openAction();
    setOpen(true);
  };
  const handleCloseAction = () => {
    typeof props.closeAction === 'function' && props.closeAction();
    setOpen(false);
  };
  const t = useTrans();
  return (
    <React.Fragment>
      <Button
        color={'secondary'}
        onClick={handleOpenAction}
        {...props.ButtonProps}
        className={`delete-btn ${props.ButtonProps?.className || ''}`}
      >
        {t('app.delete.multiple.btn')}
      </Button>
      <Dialog onClose={handleCloseAction} open={props.open !== undefined ? props.open : open}>
        <DialogTitle> {t('app.delete.confirm.title')}</DialogTitle>
        <DialogContent>{props.children || t('app.delete.confirm.msg')}</DialogContent>
        <DialogActions>
          <Button onClick={handleCloseAction} color="default">
            {t('app.cancel')}
          </Button>
          {!props.isLoading ? (
            <Button onClick={props.deleteAction} color="secondary">
              {t('app.delete.btn')}
            </Button>
          ) : (
            <Button color="secondary">
              <AppLoading message={t('app.delete.loading')} withoutBackground inline />
            </Button>
          )}
        </DialogActions>
      </Dialog>
    </React.Fragment>
  );
};
