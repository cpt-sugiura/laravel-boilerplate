import Button, { ButtonProps } from '@mui/material/Button';
import { useNavigate } from 'react-router';
import React from 'react';
import { useTrans } from '@/lang/useLangMsg';

export const ReturnBtn: React.FC<{ BtnProps?: ButtonProps }> = (props) => {
  const navigate = useNavigate();
  const t = useTrans();
  return (
    <Button className="return-btn" {...props.BtnProps} onClick={() => navigate(-1)} style={{ width: '100px' }}>
      {t('app.return')}
    </Button>
  );
};
