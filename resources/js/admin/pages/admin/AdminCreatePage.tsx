import React from 'react';
import { useMessageDialog } from '@/common/context/DialogMessageContext';
import { makeErrorResponseMessage, makeSuccessResponseMessage } from '@/common/hook/makeUseAxios';
import { useNavigate } from 'react-router';
import { makeRoutePath, useAppRouting } from '@/admin/Router';
import { FormProvider, useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import Button, { ButtonProps } from '@mui/material/Button';
import Grid from '@mui/material/Grid';
import FormControl from '@mui/material/FormControl';
import { AppLoading } from '@/common/component/AppLoading';
import Paper from '@mui/material/Paper';
import { HasIsolateItemBox } from '@/common/component/HasIsolateItemBox';
import { ReturnBtn } from '@/admin/component/_common/ReturnBtn';
import { useAdminAxios } from '@/admin/hook/API/useAdminAxios';
import { useYup } from '@/common/validation/BaseYup';
import { AdminFormFields, AdminFormInputTypes } from '@/admin/component/admin/AdminFormFields';
import { ResponseMessageTypography } from '@/common/component/ResponseMessageTypography';

const useSchema = () => {
  const yup = useYup();
  return yup.object().shape({
    name: yup.string().required().max(255).label('名前'),
    email: yup.string().nullable().email().max(255).label('メールアドレス'),
    password: yup.string().required().password().label('パスワード'),
    passwordConfirm: yup
      .string()
      .required()
      .oneOf([yup.ref('password')], 'パスワードと一致していません。')
      .label('パスワード（確認）'),
  });
};

const AdminCreatePage: React.FC = () => {
  const { setDialog } = useMessageDialog();
  const navigate = useNavigate();
  const AppRouting = useAppRouting();

  const submitAxios = useAdminAxios<AdminFormInputTypes>();

  const createAction = (fields: AdminFormInputTypes) => {
    submitAxios.axiosInstance
      .post(`/admin`, fields)
      .then((response) => {
        setDialog('作成結果', <ResponseMessageTypography msg={makeSuccessResponseMessage(response)} />);
        navigate(makeRoutePath(AppRouting.adminShow, { adminId: response.data.body.adminId }));
      })
      .catch((error) => setDialog('作成結果', <ResponseMessageTypography msg={makeErrorResponseMessage(error)} />));
  };
  const schema = useSchema();
  const formMethods = useForm<AdminFormInputTypes>({ resolver: yupResolver(schema) });
  const onSubmit: ButtonProps['onClick'] = formMethods.handleSubmit(() => {
    createAction(formMethods.getValues());
  });
  return (
    <div className="form-page-content-root">
      <HasIsolateItemBox justifyContent="flex-start">
        <ReturnBtn />
      </HasIsolateItemBox>
      <FormProvider {...formMethods}>
        <form onSubmit={formMethods.handleSubmit(() => createAction(formMethods.getValues()))} autoComplete="off">
          <Paper className={'form-paper'}>
            <AdminFormFields />
            <Grid item xs={12}>
              <FormControl>
                {!submitAxios.isLoading ? (
                  <Button onClick={onSubmit}> 作成 </Button>
                ) : (
                  <AppLoading message={'作成中'} />
                )}
              </FormControl>
            </Grid>
          </Paper>
        </form>
      </FormProvider>
    </div>
  );
};

const memo = React.memo(AdminCreatePage);
export { memo as AdminCreatePage };
