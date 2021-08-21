import Typography from '@material-ui/core/Typography';
import React from 'react';
import { ResponseMessage } from '@/common/hook/makeUseAxios';

const ResponseMessageTypography: React.FC<{ msg: ResponseMessage }> = ({ msg }) => {
  return msg.hasError ? <Typography className={'error'}>{msg.msg}</Typography> : <Typography>{msg.msg}</Typography>;
};
export { ResponseMessageTypography };
