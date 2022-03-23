export const urlToFile = async (url: string): Promise<File> => {
  return fetch(url)
    .then((response) => response.blob())
    .then((blob) => new File([blob], url.replace(/.*([^/]*$)/, '')));
};

/**
 * Blob にできるデータをダウンロード
 * @param data
 * @param fileName
 * @param mimeType
 */
export const downloadData = (data: BlobPart, fileName: string, mimeType: string): void => {
  const blob = new Blob([data], {
    type: mimeType,
  });
  const url = window.URL.createObjectURL(blob);
  downloadURL(url, fileName);
  setTimeout(function () {
    return window.URL.revokeObjectURL(url);
  }, 1000);
};

/**
 * URL のファイルを fileName でダウンロード.
 * @param url ダウンロード先
 * @param fileName a タグ先の header でファイル名が指定されているとそちらを優先
 */
export const downloadURL = (url: string, fileName?: string): void => {
  const a = document.createElement('a');
  a.href = url;
  a.download = fileName || url.replace(/^.*\//, '');
  document.body.appendChild(a);
  a.style.display = 'none';
  a.click();
  a.remove();
};
