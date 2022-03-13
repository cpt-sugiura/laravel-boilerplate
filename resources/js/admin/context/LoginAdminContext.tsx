import React, { PropsWithChildren, useContext, useState } from 'react';
import { getLoginAdminJSON } from '@/admin/repository/html/HtmlHead';

type AdminEntity = {
  adminId: number;
  name: string;
  email: string;
};
type LoginAdminContextType = {
  loginAdmin: AdminEntity;
  setLoginAdmin: (loginAdmin: AdminEntity) => void;
};
const getAdminInMeta = (): AdminEntity => {
  const json = getLoginAdminJSON();
  return JSON.parse(json);
};
const makeAdminEntityInit = (): AdminEntity => {
  return {
    adminId: -1,
    name: '',
    email: '',
  };
};
export const LoginAdminContext = React.createContext<LoginAdminContextType>({
  loginAdmin: makeAdminEntityInit(),
  // eslint-disable-next-line @typescript-eslint/no-empty-function,@typescript-eslint/no-unused-vars
  setLoginAdmin: (loginAdmin: AdminEntity) => {},
});

export const useLoginAdminContext = (): LoginAdminContextType => useContext(LoginAdminContext);
export const LoginAdminProvider = LoginAdminContextComponent;

/**
 * ログインしているアカウントを扱う
 * @constructor
 */
function LoginAdminContextComponent(props: PropsWithChildren<{}>): JSX.Element {
  const [loginAdmin, setLoginAdmin] = useState<AdminEntity>(getAdminInMeta());

  return (
    <LoginAdminContext.Provider
      value={{
        loginAdmin,
        setLoginAdmin,
      }}
    >
      {props.children}
    </LoginAdminContext.Provider>
  );
}
