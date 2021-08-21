import React from 'react';
import Typography from '@material-ui/core/Typography';

export const PageTitle = React.memo(PageTitleComponent);
type Props = {
  title: string;
};

/**
 * ページのタイトル
 * @param props
 * @constructor
 */
function PageTitleComponent(props: Props) {
  return (
    <Typography variant="h6" gutterBottom>
      {props.title}
    </Typography>
  );
}
