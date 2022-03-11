import React, { createRef, FormEvent } from 'react';
import Button from '@mui/material/Button';
import { ColBox } from '@/common/component/ColBox';
import { csrfToken, getMetaContent } from '@/admin/repository/html/HtmlHead';
import TextField from '@mui/material/TextField';
import { errorColor } from '@/admin/theme';
import { AppLoading } from '@/common/component/AppLoading';
import { Checkbox, FormControlLabel } from '@mui/material';
import { Logo } from '@/admin/_WhenNotLogin/Logo';
import { useAdminAxios } from '@/admin/hook/API/useAdminAxios';
import {useOnEnterKey} from "@/common/hook/useOnEnterKey";
import {NavLink} from "react-router-dom";

type AuthFormInputs = {
  email: string;
  password: string;
  remember: boolean;
};

export const LoginPage: React.FC = () => {
  const { axiosInstance, responseErrors, responseMessage } = useAdminAxios<AuthFormInputs>(false);
  const [isLoading, setIsLoading] = React.useState(false);
  const formRef = createRef<HTMLFormElement>();
  const loginAction = () => {
    if (!formRef.current) {
      return;
    }
    setIsLoading(true);
    axiosInstance
      .post('/login', new FormData(formRef.current))
      .then(() => (window.location.href = getMetaContent('next-url')))
      .catch(() => setIsLoading(false));
  };
  const handleSubmit: HTMLFormElement['onSubmit'] = (e: FormEvent) => {
    e.preventDefault();
    loginAction();
  };
  const { onEnterKey } = useOnEnterKey(loginAction);

  return (
    <ColBox className="root-container">
      <form ref={formRef} onSubmit={handleSubmit} onKeyDown={onEnterKey} className="root-form">
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
            name={'email'}
            error={!!responseErrors?.email}
            helperText={responseErrors?.email}
          />
          <TextField
            label={'パスワード'}
            required
            type="password"
            name={'password'}
            error={!!responseErrors?.password}
            helperText={responseErrors?.password}
          />
          <FormControlLabel control={<Checkbox name={'remember'} />} label="ログインを継続する" />
        </ColBox>
        <div style={responseMessage.hasError ? { color: errorColor.main } : {}}>{responseMessage.msg}</div>
        {!isLoading ? <Button type={'submit'}>ログイン</Button> : <AppLoading message={'ログイン中'} />}
      </form>
       <NavLink to={'/send_password_reset'}>
        <Button className="back-btn">パスワードを忘れた方はこちら</Button>
       </NavLink>
    </ColBox>
  );
};
