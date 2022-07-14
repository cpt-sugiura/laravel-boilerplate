import React from 'react';
import { HasIsolateItemBox } from '@/common/component/HasIsolateItemBox';
import { ToCreatePageBtn } from '@/{{ lcfirst($domain) }}/component/_common/ToCreatePageBtn';
import { useAppRouting } from '@/{{ lcfirst($domain) }}/Router';
import './{{ $classBaseName }}SearchPage.scss';
import { {{ $classBaseName }}SearchBoxAndTable } from '@/{{ lcfirst($domain) }}/component/{{ lcfirst($classBaseName) }}/{{ $classBaseName }}SearchBoxAndTable';

export const {{ $classBaseName }}SearchPage: React.FC = () => {
  const appRouting = useAppRouting();
  return (
    <div className={'{{ \Str::kebab(lcfirst($classBaseName)) }}-search-page'}>
      <HasIsolateItemBox>
        <ToCreatePageBtn toRoute={appRouting.{{ lcfirst($classBaseName) }}Create} />
      </HasIsolateItemBox>
      <{{ $classBaseName }}SearchBoxAndTable />
    </div>
  );
};
