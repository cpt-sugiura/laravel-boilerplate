import { makeUseAxios } from '@/common/hook/makeUseAxios';
import { createBaseRepository } from '@/admin/repository/BaseRepository';

const useAdminAxios = makeUseAxios(createBaseRepository);
export { useAdminAxios };
