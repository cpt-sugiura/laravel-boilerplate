import React from 'react';
import { useMessageDialog } from '@/common/context/DialogMessageContext';
import { makeErrorResponseMessage, makeSuccessResponseMessage } from '@/common/hook/makeUseAxios';
import { useNavigate } from 'react-router';
import { makeRoutePath, useAppRouting } from '@/{{ lcfirst($domain) }}/Router';
import { FormProvider, useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import Button, { ButtonProps } from '@mui/material/Button';
import Grid from '@mui/material/Grid';
import FormControl from '@mui/material/FormControl';
import { AppLoading } from '@/common/component/AppLoading';
import Paper from '@mui/material/Paper';
import { HasIsolateItemBox } from '@/common/component/HasIsolateItemBox';
import { ReturnBtn } from '@/{{ lcfirst($domain) }}/component/_common/ReturnBtn';
import { useAccountAxios } from '@/{{ lcfirst($domain) }}/hook/API/useAccountAxios';
import { useYup } from '@/common/validation/BaseYup';
import { {{ $classBaseName }}FormFields, {{ $classBaseName }}FormInputTypes } from '@/{{ lcfirst($domain) }}/component/{{ \Str::camel($classBaseName) }}/{{ $classBaseName }}FormFields';
import { ResponseMessageTypography } from '@/common/component/ResponseMessageTypography';

const useSchema = () => {
  const yup = useYup();
  return yup.object().shape({
@foreach( array_filter($columnsForRender, static fn($c) => $c->editable()) as $c )
    {{ $c->getNameAsRequestKey() }}: {!! $c->getReactYupValidation() !!},
@endforeach
  });
};

export const {{ $classBaseName }}CreatePage: React.FC = () => {
  const { setDialog } = useMessageDialog();
  const navigate = useNavigate();
  const AppRouting = useAppRouting();

  const submitAxios = useAccountAxios<{{ $classBaseName }}FormInputTypes>();

  const createAction = (fields: {{ $classBaseName }}FormInputTypes) => {
    submitAxios.axiosInstance
      .post(`/{{ \Str::snake($classBaseName) }}`, fields)
      .then((response) => {
        setDialog('作成結果', <ResponseMessageTypography msg={makeSuccessResponseMessage(response)} />);
        navigate(makeRoutePath(AppRouting.{{ \Str::camel($classBaseName) }}Show, { {{ \Str::camel($primaryKey) }}: response.data.body.{{ \Str::camel($primaryKey) }} }));
      })
      .catch((error) => setDialog('作成結果', <ResponseMessageTypography msg={makeErrorResponseMessage(error)} />));
  };
  const schema = useSchema();
  const formMethods = useForm<{{ $classBaseName }}FormInputTypes>({ resolver: yupResolver(schema) });
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
            <{{ $classBaseName }}FormFields />
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
