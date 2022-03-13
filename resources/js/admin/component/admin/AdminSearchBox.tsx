import React, { ChangeEvent, useState } from 'react';
import Paper from '@mui/material/Paper';
import { RowBox } from '@/common/component/RowBox';
import Button from '@mui/material/Button';
import { useNavigate } from 'react-router';
import { SearchBtn } from '@/admin/component/_common/SearchBtn';
import { ResetBtn } from '@/admin/component/_common/ResetBtn';
import { useAppRouting } from '@/admin/Router';
import { AppTextField } from '@/common/component/fields/AppTextField';
import './AdminSearchBox.scss';

type Props = {
  clickSearch: (searchBox: { [key: string]: string | number | Array<string | number> }) => void;
  defaultSearchBoxValues?: { [key: string]: string | number | Array<string | number> };
};

export const AdminSearchBox = React.memo(AdminSearchBoxComponent);

/**
 * 検索ボックス
 * @param props
 * @constructor
 */
function AdminSearchBoxComponent(props: Props): JSX.Element {
  const navigate = useNavigate();
  const [searchBox, setSearchBox] = useState({
    name: '',
    email: '',
    ...props.defaultSearchBoxValues,
  });
  const updateState = (event: ChangeEvent<HTMLInputElement> | React.ChangeEvent<{ name?: string; value: unknown }>) => {
    if (!event.target.name) {
      return;
    }
    setSearchBox({
      ...searchBox,
      [event.target.name]: event.target.value,
    });
  };
  const clearSearch = () => {
    setSearchBox({
      name: '',
      email: '',
    });
  };
  const AppRouting = useAppRouting();
  return (
    <Paper className="admin-search-box search-box" elevation={3}>
      <AppTextField label="管理者名" name="name" onChange={updateState} value={searchBox.name} />
      <AppTextField label="メールアドレス" name="email" onChange={updateState} value={searchBox.email} />
      <Button className={'create-btn'} onClick={() => navigate(AppRouting.adminCreate.path)}>
        管理者作成
      </Button>
      <RowBox className={'control'}>
        <SearchBtn onClick={() => props.clickSearch(searchBox)} />
        <ResetBtn onClick={clearSearch} />
      </RowBox>
    </Paper>
  );
}
