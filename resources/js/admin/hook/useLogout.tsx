import { useAdminAxios } from '@/admin/hook/API/useAdminAxios';

type useLogoutRet = {
  logoutAction: () => void;
  logoutLoading: boolean;
};
export const useLogout = (): useLogoutRet => {
  const { axiosInstance, isLoading } = useAdminAxios(false);
  const logoutAction = () => axiosInstance.post('/logout').then(() => (window.location.href = '/admin/login'));
  // todo err handling
  // .catch(() => (window.location.href = '/admin/login'));

  return {
    logoutAction,
    logoutLoading: isLoading,
  };
};
