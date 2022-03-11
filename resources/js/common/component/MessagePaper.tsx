import React from 'react';
import Paper from '@mui/material/Paper';
import Typography from '@mui/material/Typography';

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
