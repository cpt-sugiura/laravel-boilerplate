import Button from '@mui/material/Button';
import { useNavigate } from 'react-router';
import React from 'react';

export const ReturnBtn: React.FC = () => {
  const navigate = useNavigate();
  return (
    <Button className="back-btn" onClick={() => navigate(-1)}>
      戻る
    </Button>
  );
};
