export type BaseResponse<T> = {
  success: boolean;
  body: T;
  message: string;
};

export type BaseSearchResponse<T> = BaseResponse<{
  data: T[];
  perPage: number;
  total: number;
  currentPage: number;
  lastPage: number;
  from: number;
  to: number;
}>;
