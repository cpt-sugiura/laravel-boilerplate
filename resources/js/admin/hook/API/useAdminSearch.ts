import { SearchRequestObject, useSearch, UseSearchReturn } from '@/common/hook/useSearch';
import { useAdminAxios } from '@/admin/hook/API/useAdminAxios';

export function useAdminSearch<T = never>(
  requestUrl: string,
  defaultSearchRequest: Partial<SearchRequestObject> = {},
  withoutWriteURL = false
): UseSearchReturn<T> {
  const { axiosInstance } = useAdminAxios();
  return useSearch(requestUrl, axiosInstance, defaultSearchRequest, withoutWriteURL);
}
