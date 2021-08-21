import { useState } from 'react';
import { AxiosInstance, AxiosRequestConfig } from 'axios';

/**
 * URL のファイルを fileName でダウンロード.
 * @param url ダウンロード先
 * @param fileName a タグ先の header でファイル名が指定されているとそちらを優先
 */
const downloadURL = (url: string, fileName?: string): void => {
  const a = document.createElement('a');
  a.href = url;
  a.download = fileName || url.replace(/^.*\//, '');
  document.body.appendChild(a);
  a.style.display = 'none';
  a.click();
  a.remove();
};

export type Downloader = {
  run: (axiosParams?: AxiosRequestConfig['params']) => void;
  isDownloading: boolean;
  progressPer: number;
  loadedByte: number;
};

export const useDownloader = (
  axiosInstance: AxiosInstance,
  url: string,
  params: AxiosRequestConfig['params']
): Downloader => {
  const [isDownloading, setIsLoading] = useState(false);
  const [progressPer, setProgressPer] = useState(0);
  const [loadedByte, setLoadedByte] = useState(0);

  const axiosRunner = (argParams?: AxiosRequestConfig['params']) =>
    axiosInstance
      .get(url, {
        params: { ...params, ...argParams },
        onDownloadProgress: (e) => {
          setLoadedByte(e.loaded);
          if (e.lengthComputable) {
            setProgressPer((e.loaded / e.total) * 100);
          }
        },
      })
      .then((response) => {
        const filename = decodeURI(
          response.headers['content-disposition'].replace(/.*filename\*=utf-8''(.*)(; )?/, '$1')
        );
        let blobParts;
        if (filename.match(/(\.csv|\.CSV)$/)) {
          blobParts = [new Uint8Array([0xef, 0xbb, 0xbf]), response.data];
        } else {
          blobParts = [response.data];
        }
        const objectUrl = window.URL.createObjectURL(new Blob(blobParts));
        downloadURL(objectUrl, filename);
        setIsLoading(false);
      })
      .finally(() => setIsLoading(false));
  const run = (axiosParams?: AxiosRequestConfig['params']) => {
    setLoadedByte(0);
    setProgressPer(0);
    setIsLoading(true);
    axiosRunner(axiosParams);
  };

  return {
    run,
    isDownloading,
    progressPer,
    loadedByte,
  };
};
