import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter,  Route,  } from 'react-router-dom';
import { theme } from '@/admin/theme';
import ErrorBoundary from '@/common/ErrorBoundary';
import { DialogMessagesProvider } from '@/common/context/DialogMessageContext';
import { LoginPage } from '@/admin/_WhenNotLogin/LoginPage';
import { SendPasswordResetPage } from '@/admin/_WhenNotLogin/SendPasswordResetPage';
import './not_login.scss';
import { RunPasswordResetPage } from '@/admin/_WhenNotLogin/RunPasswordResetPage';
import { LangLocaleProvider, messages, useLangLocaleContext } from '@/lang/messageLoader';
import { IntlProvider } from 'react-intl';
import {ThemeProvider} from "@mui/material";
import {Redirect, Routes} from "react-router";

/**
 * ルートコンポーネント
 * @constructor
 */
function App() {
  const { langLocale } = useLangLocaleContext();
  return (
    <IntlProvider key={langLocale} locale={langLocale} messages={messages[langLocale]}>
      <BrowserRouter basename={'admin'}>
        <ThemeProvider theme={theme}>
          <DialogMessagesProvider>
            <Routes>
              <Route path="/login">
                <LoginPage />
              </Route>
              <Route path="/send_password_reset">
                <SendPasswordResetPage />
              </Route>
              <Route path="/password/reset/:token" element={RunPasswordResetPage} />
              <Route path="*" element={() => <Redirect to="/login" />}/>
            </Routes>
          </DialogMessagesProvider>
        </ThemeProvider>
      </BrowserRouter>
    </IntlProvider>
  );
}
/**
 * 真のルートコンポーネント。言語プロバイダの中でフックを使う必要があるのでこの形
 */
const AppWrapper = () => {
  return (
    <ErrorBoundary>
      <LangLocaleProvider>
        <App />
      </LangLocaleProvider>
    </ErrorBoundary>
  );
};
ReactDOM.render(<AppWrapper />, document.getElementById('app'));
