import React from 'react';
import { AppFormTextField } from '@/common/component/form/AppFormTextField';
import './AdminFormFields.scss';

type AdminFormInputTypes = {
  name: string;
  email: string;
  password: string;
  passwordConfirm: string;
};

const toAdminFormInputTypes = (any: Partial<AdminFormInputTypes>): AdminFormInputTypes => {
  return {
    name: any.name || '',
    email: any.email || '',
    password: any.password || '',
    passwordConfirm: any.passwordConfirm || '',
  };
};

/**
 * 管理者フォームフィールドコンポーネント
 */
function AdminFormFields(): JSX.Element {
  return (
    <div className="admin-form-fields-root">
      <AppFormTextField name="name" label="名前" />
      <AppFormTextField name="email" label="メールアドレス" />
      <AppFormTextField name="password" label="パスワード" type="password" />
      <AppFormTextField name="passwordConfirm" label="パスワード（確認）" type="password" />
    </div>
  );
}

export { AdminFormFields, toAdminFormInputTypes };
export type { AdminFormInputTypes };
