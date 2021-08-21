import Button from '@material-ui/core/Button';
import { useHistory } from 'react-router';
import React from 'react';

export const ReturnBtn: React.FC = () => {
  const history = useHistory();
  return (
    <Button color="default" onClick={() => history.goBack()}>
      戻る
    </Button>
  );
};
