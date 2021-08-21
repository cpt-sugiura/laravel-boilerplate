import React from 'react';
import Paper from '@material-ui/core/Paper';
import Typography from '@material-ui/core/Typography';

type MessagePaperProps = {
  message: string;
};

export const MessagePaper: React.FC<MessagePaperProps> = ({ message }) => {
  return (
    <Paper
      style={{
        padding: '1em',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
      }}
    >
      <Typography>{message}</Typography>
    </Paper>
  );
};
