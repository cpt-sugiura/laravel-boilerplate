import React, { FormEvent } from 'react';
import Button from '@mui/material/Button';
import { ColBox } from '@/common/component/ColBox';
import { csrfToken } from '@/admin/repository/html/HtmlHead';
import TextField, { TextFieldProps } from '@mui/material/TextField';
import { errorColor, primaryColor } from '@/admin/theme';
import { AppLoading } from '@/common/component/AppLoading';
import {NavLink, useMatch, matchPath} from 'react-router-dom';
import {useLocation, useParams} from 'react-router';
import { Logo } from '@/admin/_WhenNotLogin/Logo';
import { useAdminAxios } from '@/admin/hook/API/useAdminAxios';

type AuthFormInputs = {
  email: string;
  password: string;
  passwordConfirm: string;
  token: string;
};

const Control: React.FC<{ isLoading: boolean; isSuccess: boolean }> = ({ isLoading, isSuccess }) => {
  if (isSuccess) {
    return (
      <NavLink to={'/login'}>
        <Button>ログインページへ移動</Button>
      </NavLink>
    );
  }
  if (isLoading) {
    return <AppLoading message={'パスワード再設定中'} />;
  }
  return <Button type={'submit'}>パスワード再設定</Button>;
};

export const RunPasswordResetPage: React.FC = () => {
  let { token } = useParams();
  const [isSuccess, setIsSuccess] = React.useState(false);
  const [authParam, setAuthParam] = React.useState<AuthFormInputs>({
    email: '',
    password: '',
    passwordConfirm: '',
    token: token || '',
  });
  const makeHandleOnChange: (name: string) => TextFieldProps['onChange'] = (name) => (e) => {
    setAuthParam({ ...authParam, [name]: e.target.value });
  };
  const { isLoading, axiosInstance, responseErrors, responseMessage } = useAdminAxios<AuthFormInputs>(false);
  const handleSubmit: HTMLFormElement['onSubmit'] = async (e: FormEvent) => {
    e.preventDefault();
    await axiosInstance.post('/password/reset', authParam).then(() => setIsSuccess(true));
  };

  return (
    <ColBox className="root-container">
      <form
        method={'POST'}
        action={'/admin/password/reset'}
        onSubmit={handleSubmit}
        className="root-form"
        style={{
          background: primaryColor[50],
        }}
      >
        <Logo />
        <input type={'hidden'} value={csrfToken()} name={'_token'} />
        <ColBox
          BoxProps={{
            style: {
              display: 'grid',
              gridTemplateRows: 'repeat(2, 1fr)',
              gap: '10px',
              marginTop: '10px',
            },
          }}
        >
          <TextField
            label={'メールアドレス'}
            required
            onChange={makeHandleOnChange('email')}
            error={!!responseErrors?.email}
            helperText={responseErrors?.email}
          />
          <TextField
            label={'パスワード'}
            required
            type="password"
            onChange={makeHandleOnChange('password')}
            error={!!responseErrors?.password}
            helperText={responseErrors?.password}
          />
          <TextField
            label={'パスワード（確認）'}
            required
            type="password"
            onChange={makeHandleOnChange('passwordConfirm')}
            error={!!responseErrors?.passwordConfirm}
            helperText={responseErrors?.passwordConfirm}
          />
        </ColBox>
        <div style={responseMessage.hasError ? { color: errorColor.main } : {}}>{responseMessage.msg}</div>
        <Control isSuccess={isSuccess} isLoading={isLoading} />
      </form>
    </ColBox>
  );
};
