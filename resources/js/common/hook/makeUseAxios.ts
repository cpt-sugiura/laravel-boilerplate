import React, { useRef, useState } from 'react';
import { AxiosError, AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios';
import { useWillUnmount } from 'beautiful-react-hooks';

type ResponseMessage = {
  hasError: boolean;
  msg: string;
};

const makeSuccessResponseMessage = (response: AxiosResponse): { hasError: boolean; msg: string } => {
  return { hasError: false, msg: response.data.message };
};
const makeErrorResponseMessage = (error: AxiosError): { hasError: boolean; msg: string } => {
  return { hasError: true, msg: error.response?.data.message };
};

/**
 * axios を使うカスタムフックを生成する
 */
const makeUseAxios = (createBaseRepository: (config?: AxiosRequestConfig) => AxiosInstance) => {
  return <RequestType = unknown>(
    initIsLoading = false,
    config?: AxiosRequestConfig
  ): {
    isLoading: boolean;
    axiosInstance: AxiosInstance;
    responseMessage: ResponseMessage;
    responseErrors?: Record<keyof RequestType, string[]>;
  } => {
    // 同じ axios インスタンスを使いまわすと interceptor の混線が起きるので axios インスタンス生成関数を使用
    const axiosInstance = createBaseRepository(config);
    const [isLoading, setIsLoading] = useState(initIsLoading);
    const [responseMessage, setResponseMessage] = React.useState<ResponseMessage>({
      hasError: false,
      msg: '',
    });
    const [responseErrors, setResponseErrors] = React.useState<Record<keyof RequestType, string[]>>();

    const unmounted = useRef(false);
    useWillUnmount(() => {
      unmounted.current = true;
    });

    const requestInterceptor = (request: AxiosRequestConfig): AxiosRequestConfig => {
      !unmounted.current && setIsLoading(true);
      return request;
    };

    const responseSuccessInterceptor = (response: AxiosResponse): AxiosResponse => {
      !unmounted.current && setIsLoading(false);
      setResponseMessage({ hasError: false, msg: response.data.message });
      setResponseErrors(undefined);
      return response;
    };

    const responseFailedInterceptor = (error: AxiosError): void => {
      !unmounted.current && setIsLoading(false);
      setResponseMessage({ hasError: true, msg: error.response?.data.message });
      setResponseErrors(error.response?.data?.body?.errors);
      console.error(error);

      throw error;
    };

    axiosInstance.interceptors.request.use(requestInterceptor);
    axiosInstance.interceptors.response.use(responseSuccessInterceptor, responseFailedInterceptor);

    return {
      isLoading,
      axiosInstance,
      responseMessage,
      responseErrors,
    };
  };
};
/**
 * 定型エラーメッセージ、特にバリデーションのそれを配列で取得
 * @param {object} e
 * @return {string[]}
 */
const getErrorMessages = (e: Partial<AxiosError> & Error): string[] => {
  console.error(e);
  if (!e.response) {
    return [e.toString()];
  }
  if (e.response.data.body && typeof e.response.data.body.errors === 'object') {
    return [`バリデーションエラーがありました。入力内容をご確認ください。`];
  } else if (e.response.data.message) {
    return [e.response.data.message];
  } else {
    return [e.response.toString()];
  }
};

export type { ResponseMessage };
export { makeUseAxios, makeSuccessResponseMessage, makeErrorResponseMessage, getErrorMessages };
