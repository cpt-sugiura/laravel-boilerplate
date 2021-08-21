import Qs from 'qs';
import axios, { AxiosInstance, AxiosRequestConfig } from 'axios';

/**
 * 設定の付いた axios を返す
 */
function createAxiosInstance(): AxiosInstance {
  const baseConfig: AxiosRequestConfig = {
    baseURL: '/admin-browser-api/',
    timeout: 60000, // ms
    /**
     * @param {Object|String} params
     * @return {String}
     */
    paramsSerializer: (params) => {
      return Qs.stringify(params, { arrayFormat: 'brackets' });
    },
    headers: {
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  };
  const instance = axios.create(baseConfig);
  instance.interceptors.request.use((config) => {
    config.url = config.url?.replace(/\/$/, '');

    return config;
  });
  return axios.create(baseConfig);
}

export const createBaseRepository = createAxiosInstance;
