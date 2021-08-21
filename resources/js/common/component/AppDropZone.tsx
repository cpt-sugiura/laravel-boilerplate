import * as React from 'react';
import { PropsWithChildren, useCallback } from 'react';
import { useDropzone } from 'react-dropzone';
import { useTrans } from '@/lang/useLangMsg';

export const AppDropZone = DropZoneComponent;

type Props = {
  onChangeFile: (file: File) => void;
  accept?: string | string[];
};

const styles = {
  root: {
    minHeight: '10em',
    background: 'rgba(0,0,0,0.075)',
    border: 'dotted',
    padding: '0.5em',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
  },
};

/**
 * ドラッグアンドドロップでファイルを操作
 * @constructor
 */
function DropZoneComponent(props: PropsWithChildren<Props>): JSX.Element {
  const t = useTrans('form.dropzone.');
  const onDrop = useCallback(
    (acceptedFiles) => {
      acceptedFiles[0] instanceof File && props.onChangeFile(acceptedFiles[0]);
    },
    [props.onChangeFile]
  );

  const { getRootProps, getInputProps, isDragActive } = useDropzone({
    onDrop,
    accept: props.accept || undefined,
    noClick: !!props.children,
  });

  const renderWithChildren = (): JSX.Element => (
    <div {...getRootProps()} style={{ width: '100%', height: '100%' }}>
      <input {...getInputProps()} />
      {props.children}
    </div>
  );

  const renderWithoutChildren = (): JSX.Element => (
    <div {...getRootProps()} style={styles.root}>
      <input {...getInputProps()} />
      {isDragActive ? <p>{t('dragging')}</p> : <p>{t('description')}</p>}
    </div>
  );

  return props.children ? renderWithChildren() : renderWithoutChildren();
}
