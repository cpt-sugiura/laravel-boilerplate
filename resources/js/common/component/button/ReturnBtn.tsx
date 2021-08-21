import Button, { ButtonProps } from '@material-ui/core/Button';
import { useHistory } from 'react-router';
import React from 'react';
import { useTrans } from '@/lang/useLangMsg';

export const ReturnBtn: React.FC<{ BtnProps?: ButtonProps }> = (props) => {
  const history = useHistory();
  const t = useTrans();
  return (
    <Button color="default" {...props.BtnProps} onClick={() => history.goBack()} style={{ width: '100px' }}>
      {t('app.return')}
    </Button>
  );
};
