import React, { FormEvent } from 'react';
import Button from '@mui/material/Button';
import { ColBox } from '@/common/component/ColBox';
import { csrfToken } from '@/admin/repository/html/HtmlHead';
import TextField, { TextFieldProps } from '@mui/material/TextField';
import { errorColor, primaryColor } from '@/admin/theme';
import { AppLoading } from '@/common/component/AppLoading';
import { NavLink } from 'react-router-dom';
import { Logo } from '@/admin/_WhenNotLogin/Logo';
import { useAdminAxios } from '@/admin/hook/API/useAdminAxios';

type AuthFormInputs = {
  email: string;
};

const SendPasswordResetPage: React.FC = () => {
  const [authParam, setAuthParam] = React.useState<AuthFormInputs>({
    email: '',
  });
  const makeHandleOnChange: (name: string) => TextFieldProps['onChange'] = (name) => (e) => {
    setAuthParam({ ...authParam, [name]: e.target.value });
  };
  const { isLoading, axiosInstance, responseErrors, responseMessage } = useAdminAxios<AuthFormInputs>(false);
  const handleSubmit: HTMLFormElement['onSubmit'] = async (e: FormEvent) => {
    e.preventDefault();
    await axiosInstance.post('password/send_reset_mail', authParam);
  };

  return (
    <ColBox className="root-container">
      <form
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
              gridTemplateRows: 'repeat(1, 1fr)',
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
        </ColBox>
        <div style={responseMessage.hasError ? { color: errorColor.main } : {}}>{responseMessage.msg}</div>
        {!isLoading ? <Button type={'submit'}>パスワードリセットメール送信</Button> : <AppLoading message="送信中" />}
      </form>
      <NavLink to={'/login'}>
        <Button className="back-btn">ログインページに戻る</Button>
      </NavLink>
    </ColBox>
  );
};

export { SendPasswordResetPage };
