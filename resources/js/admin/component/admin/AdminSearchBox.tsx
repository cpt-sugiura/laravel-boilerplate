import React, { ChangeEvent, useState } from 'react';
import Paper from '@mui/material/Paper';
import TextField from '@mui/material/TextField';
import Grid from '@mui/material/Grid';
import { RowBox } from '@/common/component/RowBox';
import Button from '@mui/material/Button';
import { useNavigate } from 'react-router';
import { SearchBtn } from '@/admin/component/_common/SearchBtn';
import { ResetBtn } from '@/admin/component/_common/ResetBtn';
import { useAppRouting } from '@/admin/Router';

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
    });
  };
  const AppRouting = useAppRouting();
  return (
    <Paper className="admin-search-box search-box without-grid" elevation={3}>
      <Grid container>
        <Grid item xs={3}>
          <TextField label="管理者名" name="name" onChange={updateState} value={searchBox.name} />
        </Grid>
        <Grid item xs={3}>
          <RowBox>
            <SearchBtn onClick={() => props.clickSearch(searchBox)} />
            <ResetBtn onClick={clearSearch} />
          </RowBox>
        </Grid>
        <Grid item xs={4} />
        <Grid item xs={2}>
          <Button onClick={() => navigate(AppRouting.adminCreate.path)}>管理者作成</Button>
        </Grid>
      </Grid>
    </Paper>
  );
}
