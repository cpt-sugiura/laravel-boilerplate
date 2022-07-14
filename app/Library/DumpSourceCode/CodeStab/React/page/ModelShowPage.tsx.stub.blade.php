import React, { useEffect } from 'react';
import { AppLoading } from '@/common/component/AppLoading';
import { useMessageDialog } from '@/common/context/DialogMessageContext';
import { isAxiosError, makeErrorResponseMessage, makeSuccessResponseMessage } from '@/common/hook/makeUseAxios';
import { DeleteBtn } from '@/{{ lcfirst($domain) }}/component/_common/DeleteBtn';
import { useAppRouting, makeRoutePath } from '@/{{ lcfirst($domain) }}/Router';
import Paper from '@mui/material/Paper';
import { FormProvider, useForm } from 'react-hook-form';
import { yupResolver } from '@hookform/resolvers/yup';
import Grid from '@mui/material/Grid';
import { FormUpdateBtn } from '@/account/component/_common/FormUpdateBtn';import { HasIsolateItemBox } from '@/common/component/HasIsolateItemBox';
import { ReturnBtn } from '@/{{ lcfirst($domain) }}/component/_common/ReturnBtn';
import { useYup } from '@/common/validation/BaseYup';
import { useAccountAxios } from '@/{{ lcfirst($domain) }}/hook/API/useAccountAxios';
import {
  {{ $classBaseName }}FormFields,
  {{ $classBaseName }}FormInputTypes,
  to{{ $classBaseName }}FormInputTypes,
} from '@/{{ lcfirst($domain) }}/component/{{ \Str::camel($classBaseName) }}/{{ $classBaseName }}FormFields';
import { ResponseMessageTypography } from '@/common/component/ResponseMessageTypography';
import { useDidMount } from 'beautiful-react-hooks';
import { useNavigate, useParams } from 'react-router';

type {{ $classBaseName }}Entity = {
@foreach( $columnsForRender as $c )
  @if( $c->visible() )
  {{ $c->getNameAsRequestKey() }}: {!!$c->getTypeScriptType() !!};
  @endif
@endforeach
};

const useSchema = () => {
  const yup = useYup();
  return yup.object().shape({
@foreach( array_filter($columnsForRender, static fn($c) => $c->editable()) as $c )
    {{ $c->getNameAsRequestKey() }}: {!! $c->getReactYupValidation() !!},
@endforeach
  });
};

export const {{ $classBaseName }}ShowPage: React.FC = () => {
  const { {{ \Str::camel($primaryKey) }} } = useParams<'{{ \Str::camel($primaryKey) }}'>();
  const navigate = useNavigate();
  const [{{ \Str::camel($classBaseName) }}, set{{ $classBaseName }}] = React.useState<{{ $classBaseName }}Entity>({
@foreach( $columnsForRender as $c )
    @if( $c->visible() )
    {{ $c->getNameAsRequestKey() }}: {!!$c->getTypeScriptDefaultValue() !!},
    @endif
@endforeach
  });
  const [errorMessage, setErrorMessage] = React.useState('');
  const { setDialog } = useMessageDialog();
  const AppRouting = useAppRouting();

  const updateAxios = useAccountAxios<{{ $classBaseName }}FormInputTypes>();
  const updateAction = (fields: {{ $classBaseName }}FormInputTypes) => {
    updateAxios.axiosInstance
      .post(`/{{ \Str::snake($classBaseName) }}/${{ '{'. \Str::camel($primaryKey) .'}' }}`, fields)
      .then((response) =>
        setDialog('更新結果', <ResponseMessageTypography msg={makeSuccessResponseMessage(response)} />)
      )
      .catch((error) => setDialog('更新結果', <ResponseMessageTypography msg={makeErrorResponseMessage(error)} />));
  };

  const deleteAxios = useAccountAxios();
  const deleteAction = () => {
    deleteAxios.axiosInstance
      .delete(`{{ \Str::snake($classBaseName) }}/${{ '{'. \Str::camel($primaryKey) .'}' }}`)
      .then((response) => {
        setDialog('削除結果', <ResponseMessageTypography msg={makeSuccessResponseMessage(response)} />);
        navigate(makeRoutePath(AppRouting.{{ \Str::camel($classBaseName) }}Search));
      })
      .catch((error) => setDialog('削除結果', <ResponseMessageTypography msg={makeErrorResponseMessage(error)} />));
  };

  const { axiosInstance, isLoading } = useAccountAxios();
  useDidMount(async (): Promise<void> => {
    try {
      const response = await axiosInstance.get(`/{{ \Str::snake($classBaseName) }}/${{ '{'. \Str::camel($primaryKey) .'}' }}`);
      set{{ $classBaseName }}(response.data.body);
    } catch (e) {
      if (isAxiosError(e)) {
        setErrorMessage(`ステータスコード: ${e.response?.status}\n${e.response?.data?.body?.message || e.toString()}`);
      } else {
        setErrorMessage(`${e}`);
        throw e;
      }
    }
  });
  const schema = useSchema();
  const formMethods = useForm<{{ $classBaseName }}FormInputTypes>({ resolver: yupResolver(schema), defaultValues: {{ \Str::camel($classBaseName) }} || {} });
  useEffect(() => {
    formMethods.reset(to{{ $classBaseName }}FormInputTypes({{ \Str::camel($classBaseName) }}));
  }, [{{ \Str::camel($classBaseName) }}]);
  const onSubmit = formMethods.handleSubmit(() => {
    updateAction(formMethods.getValues());
  });

  if (isLoading) {
    return <AppLoading message={'ロード中です'} />;
  }

  if (!{{ \Str::camel($classBaseName) }}?.{{ \Str::camel($primaryKey) }}) {
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
          <{{ $classBaseName }}FormFields />
          <Grid item xs={12}>
<FormUpdateBtn isUpdating={updateAxios.isLoading} onClick={onSubmit} />
          </Grid>
        </Paper>
      </FormProvider>

      <HasIsolateItemBox>
        <DeleteBtn isDeleting={deleteAxios.isLoading} deleteAction={deleteAction} />
      </HasIsolateItemBox>
    </div>
  );
};
