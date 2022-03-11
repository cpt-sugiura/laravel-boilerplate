import React, { useEffect } from 'react';
import { RouteComponentProps } from 'react-router';
import { AppLoading } from '@/common/component/AppLoading';
import { useMessageDialog } from '@/common/context/DialogMessageContext';
import { makeErrorResponseMessage, makeSuccessResponseMessage } from '@/common/hook/makeUseAxios';
import { DeleteBtn } from '@/admin/component/_common/DeleteBtn';
import { useAppRouting, makeRoutePath } from '@/admin/Router';
import Paper, {PapreProps} from '@mui/material/Paper';
import { FormProvider, useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import Grid from '@mui/material/Grid';
import FormControl from '@mui/material/FormControl';
import Button from '@mui/material/Button';
import { HasIsolateItemBox } from '@/common/component/HasIsolateItemBox';
import { ReturnBtn } from '@/admin/component/_common/ReturnBtn';
import { useYup } from '@/common/validation/BaseYup';
import { useAdminAxios } from '@/admin/hook/API/useAdminAxios';
import { AdminFormFields, AdminFormInputTypes, toAdminFormInputTypes } from '@/admin/component/admin/AdminFormFields';
import { ResponseMessageTypography } from '@/common/component/ResponseMessageTypography';
import {AxiosError} from "axios";
import {useDidMount} from "beautiful-react-hooks";

type AdminEntity = {
  adminId: number;
  name: string;
  email: string;
  createdAt: string;
  updatedAt: string;
  canDelete: boolean;
};

const useSchema = () => {
  const yup = useYup();
  return yup.object().shape({
    name: yup.string().required().max(255).label('名前'),
    email: yup.string().nullable().email().max(255).label('メールアドレス'),
    password: yup.string().nullable().password().label('パスワード'),
    passwordConfirm: yup
      .string()
      .nullable()
      .oneOf([yup.ref('password')], 'パスワードと一致していません。')
      .label('パスワード（確認）'),
  });
};

const AdminDetailPageComponent: React.FC<RouteComponentProps<{ adminId: string }>> = (props) => {
  const [admin, setAdmin] = React.useState<AdminEntity>({
    adminId: 0,
    name: '',
    email: '',
    createdAt: '',
    updatedAt: '',
    canDelete: false,
  });
  const [errorMessage, setErrorMessage] = React.useState('');
  const { setDialog } = useMessageDialog();
  const AppRouting = useAppRouting();

  const updateAxios = useAdminAxios<AdminFormInputTypes>();
  const updateAction = (fields: AdminFormInputTypes) => {
    updateAxios.axiosInstance
      .post(`/admin/${props.match.params.adminId}`, fields)
      .then((response) =>
        setDialog('更新結果', <ResponseMessageTypography msg={makeSuccessResponseMessage(response)} />)
      )
      .catch((error) => setDialog('更新結果', <ResponseMessageTypography msg={makeErrorResponseMessage(error)} />));
  };

  const deleteAxios = useAdminAxios();
  const deleteAction = () => {
    deleteAxios.axiosInstance
      .delete(`admin/${props.match.params.adminId}`)
      .then((response) => {
        setDialog('削除結果', <ResponseMessageTypography msg={makeSuccessResponseMessage(response)} />);
        props.history.push(makeRoutePath(AppRouting.adminSearch));
      })
      .catch((error) => setDialog('削除結果', <ResponseMessageTypography msg={makeErrorResponseMessage(error)} />));
  };

  const { axiosInstance, isLoading } = useAdminAxios();
  useDidMount(async (): Promise<void> => {
    try {
      const response = await axiosInstance.get(`/admin/${props.match.params.adminId}`);
      setAdmin(response.data.body);
    } catch (e: AxiosError|Error) {
      setErrorMessage(`ステータスコード: ${e.response?.status}\n${e.response?.body?.message || e.toString()}`);
    }
  });
  const schema = useSchema();
  const formMethods = useForm<AdminFormInputTypes>({ resolver: yupResolver(schema), defaultValues: admin || {} });
  useEffect(() => {
    formMethods.reset(toAdminFormInputTypes(admin));
  }, [admin]);
  const onSubmit: PapreProps['onSubmit'] = formMethods.handleSubmit(() => {
    updateAction(formMethods.getValues());
  });

  if (isLoading) {
    return <AppLoading message={'ロード中です'} />;
  }

  if (!admin?.adminId) {
    return (
      <React.Fragment>
        <span>ローディングエラー。サーバからのレスポンスが異常です。</span>
        <pre>{errorMessage}</pre>
      </React.Fragment>
    );
  }

  return (
    <div className="form-page-content-root">
      <HasIsolateItemBox justifyContent="flex-start">
        <ReturnBtn />
      </HasIsolateItemBox>
      <FormProvider {...formMethods}>
        <Paper className={'form-paper'} component={'form'} onSubmit={onSubmit}>
          <AdminFormFields />
          <Grid item xs={12}>
            <FormControl>
              {!updateAxios.isLoading ? <Button onClick={onSubmit}> 更新 </Button> : <AppLoading message={'更新中'} />}
            </FormControl>
          </Grid>
        </Paper>
      </FormProvider>

      {admin.canDelete && (
        <HasIsolateItemBox>
          <DeleteBtn isDeleting={deleteAxios.isLoading} deleteAction={deleteAction} />
        </HasIsolateItemBox>
      )}
    </div>
  );
};

export const AdminShowPage = React.memo(AdminDetailPageComponent);
