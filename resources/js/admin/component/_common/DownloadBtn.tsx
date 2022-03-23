import Button, { ButtonProps as OriginalButtonProps } from '@mui/material/Button';
import GetAppIcon from '@mui/icons-material/GetApp';
import React from 'react';
import { infoColor } from '@/admin/theme';
import { AppLoading } from '@/common/component/AppLoading';
import { round } from '@/common/helperFunctions/number';

export type DownloadBtnProps = {
  onClick: OriginalButtonProps['onClick'];
  className?: string;
  submitMsg: string;
  downloadingMsg?: string;
  loadedByte?: number;
  ButtonProps?: OriginalButtonProps;
  disabled?: boolean;
  downloading?: boolean;
};
export const DownloadBtn: React.FC<DownloadBtnProps> = ({
  onClick,
  className,
  disabled,
  submitMsg,
  downloadingMsg,
  ButtonProps,
  downloading,
  loadedByte,
}) => {
  let defaultDownloadingMsg = `ダウンロード中`;
  if (loadedByte != null) {
    defaultDownloadingMsg += ` ${round(loadedByte / 1024 / 1024, 2).toFixed(2)}MB`;
  }

  return (
    <Button
      {...ButtonProps}
      style={{
        height: 'fit-content',
        width: 'fit-content',
        backgroundColor: infoColor.main,
        color: infoColor.contrastText,
      }}
      disabled={disabled}
      className={`submit control-btn ${className}`}
      onClick={onClick}
    >
      {downloading ? (
        <React.Fragment>
          <AppLoading
            message={downloadingMsg || defaultDownloadingMsg}
            progressStyle={{ color: infoColor.contrastText }}
            withoutBackground
            inline
          />
        </React.Fragment>
      ) : (
        <React.Fragment>
          <GetAppIcon />
          {submitMsg}
        </React.Fragment>
      )}
    </Button>
  );
};
