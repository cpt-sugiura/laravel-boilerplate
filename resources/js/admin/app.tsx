import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import { theme } from '@/admin/theme';
import { ThemeProvider } from '@mui/material/styles';
import ErrorBoundary from '@/common/ErrorBoundary';
import { AppFrame } from '@/admin/AppFrame';
import { AppRouter } from '@/admin/Router';
import { DialogMessagesProvider } from '@/common/context/DialogMessageContext';
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
            <AppFrame>
              <AppRouter />
            </AppFrame>
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
