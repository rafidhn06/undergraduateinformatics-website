export interface ApiSuccessResponse<T> {
    status: 'success';
    data: T;
    meta?: unknown;
}

export interface ApiErrorResponse {
    status: 'error';
    message?: string;
    errors?: Record<string, string[]>;
}

export type ApiResponse<T> = ApiSuccessResponse<T> | ApiErrorResponse;
