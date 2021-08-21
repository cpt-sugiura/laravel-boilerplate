import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Redirect, Route, Switch } from 'react-router-dom';
import { theme } from '@/admin/theme';
import { ThemeProvider } from '@material-ui/core/styles';
import ErrorBoundary from '@/common/ErrorBoundary';
import { DialogMessagesProvider } from '@/common/context/DialogMessageContext';
import { LoginPage } from '@/admin/_WhenNotLogin/LoginPage';
import { SendPasswordResetPage } from '@/admin/_WhenNotLogin/SendPasswordResetPage';
import './not_login.scss';
import { RunPasswordResetPage } from '@/admin/_WhenNotLogin/RunPasswordResetPage';
import { LangLocaleProvider, messages, useLangLocaleContext } from '@/lang/messageLoader';
import { IntlProvider } from 'react-intl';

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
            <Switch>
              <Route path="/login">
                <LoginPage />
              </Route>
              <Route path="/send_password_reset">
                <SendPasswordResetPage />
              </Route>
              <Route path="/password/reset/:token" component={RunPasswordResetPage} />
              <Route path="*">
                <Redirect
                  to={{
                    pathname: '/login',
                  }}
                />
              </Route>
            </Switch>
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
