/* eslint-disable @typescript-eslint/no-unused-vars */
import * as React from 'react';
import { ErrorInfo, ReactNode } from 'react';
import axios from 'axios';

type State = {
  hasError: boolean;
  error: null | Error;
  errorInfo: null | ErrorInfo;
};
// eslint-disable-next-line @typescript-eslint/ban-types
type Props = {};
/**
 * エラーをキャッチするコンポーネント
 */
export default class ErrorBoundary extends React.Component<Props, State> {
  /**
   * 初期化
   * @param props
   */
  constructor(props: Props) {
    super(props);
    this.state = {
      hasError: false,
      error: null,
      errorInfo: null,
    };
  }

  /**
   * ErrorBoundaryとして必須の関数
   * @param error
   */
  static getDerivedStateFromError(error: Error): { hasError: boolean } {
    // 次のレンダリングでフォールバック UI が表示されるように状態を更新します。
    return { hasError: true };
  }

  /**
   * マウント時に実行
   */
  componentDidCatch(error: Error, errorInfo: ErrorInfo): void {
    this.setState({ error, errorInfo });
    const headers: { [p: string]: string } = {
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    };
    const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content;
    csrfToken && (headers['X-CSRF-TOKEN'] = csrfToken);
    // エラー報告ロガーAPIにエラー内容を送信
    axios
      .post(
        '/api/logging/error',
        {
          errorName: error.name,
          errorMessage: error.message,
          errorStack: error.stack,
          errorInfo,
          userAgent: window.navigator.userAgent,
        },
        {
          timeout: 60000, // ms
          headers,
        }
      )
      .catch((e: Error) => console.error(e));
  }

  /**
   * 描画
   */
  render(): JSX.Element | ReactNode {
    if (this.state.hasError) {
      return (
        <div>
          <h1>JavaScriptの致命的エラー</h1>
          <pre>{this.state.error?.message}</pre>
          <pre>{this.state.errorInfo?.componentStack}</pre>
        </div>
      );
    }

    return this.props.children;
  }
}
