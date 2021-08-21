/* eslint-disable @typescript-eslint/no-unused-vars */
import * as React from 'react';
import { ErrorInfo, ReactNode } from 'react';

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
